<?php

namespace App\Http\Controllers\Company\Ai;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use Illuminate\Http\JsonResponse;
use App\Services\Ai\AiToolRegistry;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AiAssistantController extends Controller
{
    /**
     * Maximum number of tool-use round trips before we give up and return
     * whatever we have, so a confused loop can't run away.
     */
    private const MAX_TOOL_ITERATIONS = 6;

    /**
     * Chat with the "Ask LaunchHR" assistant, backed by DeepSeek's
     * OpenAI-compatible chat completions API. Stateless on the server - the
     * client sends the conversation history back on every request.
     *
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     */
    public function chat(Request $request, int $companyId): JsonResponse
    {
        $company = InstanceHelper::getLoggedCompany();
        $employee = InstanceHelper::getLoggedEmployee();

        if (! config('services.deepseek.api_key')) {
            return response()->json([
                'error' => 'The AI assistant isn\'t configured yet - DEEPSEEK_API_KEY is missing.',
            ], 503);
        }

        $history = $request->input('history', []);
        $messages = $history;
        $messages[] = ['role' => 'user', 'content' => $request->input('message')];

        // System prompt is only needed on the very first turn - the API is
        // stateless, but since we resend full history every time, prepending
        // it here each turn (rather than storing it in $history) keeps the
        // client-facing history free of it.
        $conversation = array_merge([
            ['role' => 'system', 'content' => $this->systemPrompt()],
        ], $messages);

        $iterations = 0;
        $response = $this->send($conversation);

        while (! empty($response['choices'][0]['message']['tool_calls']) && $iterations < self::MAX_TOOL_ITERATIONS) {
            $iterations++;
            $assistantMessage = $response['choices'][0]['message'];
            $conversation[] = $assistantMessage;
            $messages[] = $assistantMessage;

            foreach ($assistantMessage['tool_calls'] as $toolCall) {
                $arguments = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?? [];

                try {
                    $result = AiToolRegistry::dispatch($toolCall['function']['name'], $arguments, $employee, $company);
                    $content = json_encode($result);
                } catch (Exception $e) {
                    $content = 'Error: '.$e->getMessage();
                }

                $toolResultMessage = [
                    'role' => 'tool',
                    'tool_call_id' => $toolCall['id'],
                    'content' => $content,
                ];

                $conversation[] = $toolResultMessage;
                $messages[] = $toolResultMessage;
            }

            $response = $this->send($conversation);
        }

        $finalMessage = $response['choices'][0]['message'] ?? ['content' => ''];
        $messages[] = ['role' => 'assistant', 'content' => $finalMessage['content'] ?? ''];

        return response()->json([
            'reply' => $finalMessage['content'] ?? '',
            'history' => $messages,
        ]);
    }

    private function send(array $messages): array
    {
        $response = Http::withToken(config('services.deepseek.api_key'))
            ->timeout(30)
            ->post('https://api.deepseek.com/chat/completions', [
                'model' => config('services.deepseek.model'),
                'messages' => $messages,
                'tools' => AiToolRegistry::openAiDefinitions(),
                'stream' => false,
            ]);

        if ($response->failed()) {
            throw new Exception('DeepSeek API request failed: '.$response->body());
        }

        return $response->json();
    }

    private function systemPrompt(): string
    {
        return 'You are the AI assistant embedded in LaunchHR, an HR platform. '
            .'You can look up and manage the acting employee\'s time off (holidays, sick days, PTO), '
            .'and, for HR/admins, the company-wide absence report. '
            .'Only act within the tools you\'re given - never claim to have done something you didn\'t call a tool for. '
            .'Keep responses concise and conversational.';
    }
}
