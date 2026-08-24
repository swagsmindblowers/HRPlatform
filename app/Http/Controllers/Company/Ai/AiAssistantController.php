<?php

namespace App\Http\Controllers\Company\Ai;

use Exception;
use Anthropic\Client;
use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use Anthropic\Messages\TextBlock;
use Illuminate\Http\JsonResponse;
use App\Services\Ai\AiToolRegistry;
use Anthropic\Messages\ToolUseBlock;
use App\Http\Controllers\Controller;

class AiAssistantController extends Controller
{
    /**
     * Maximum number of tool-use round trips before we give up and return
     * whatever we have, so a confused loop can't run away.
     */
    private const MAX_TOOL_ITERATIONS = 6;

    /**
     * Chat with the "Ask LaunchHR" assistant. Stateless on the server - the
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

        if (! config('services.anthropic.api_key')) {
            return response()->json([
                'error' => 'The AI assistant isn\'t configured yet - ANTHROPIC_API_KEY is missing.',
            ], 503);
        }

        $history = $request->input('history', []);
        $messages = $history;
        $messages[] = ['role' => 'user', 'content' => $request->input('message')];

        $client = new Client(apiKey: config('services.anthropic.api_key'));

        $iterations = 0;
        $response = $this->send($client, $messages);

        while ($response->stopReason === 'tool_use' && $iterations < self::MAX_TOOL_ITERATIONS) {
            $iterations++;
            $toolResults = [];

            foreach ($response->content as $block) {
                if ($block instanceof ToolUseBlock) {
                    try {
                        $result = AiToolRegistry::dispatch($block->name, $block->input, $employee, $company);
                        $content = json_encode($result);
                    } catch (Exception $e) {
                        $content = 'Error: '.$e->getMessage();
                    }

                    $toolResults[] = [
                        'type' => 'tool_result',
                        'toolUseID' => $block->id,
                        'content' => $content,
                    ];
                }
            }

            $messages[] = ['role' => 'assistant', 'content' => $response->content];
            $messages[] = ['role' => 'user', 'content' => $toolResults];

            $response = $this->send($client, $messages);
        }

        $text = '';
        foreach ($response->content as $block) {
            if ($block instanceof TextBlock) {
                $text .= $block->text;
            }
        }

        $messages[] = ['role' => 'assistant', 'content' => $response->content];

        return response()->json([
            'reply' => $text,
            'history' => $messages,
        ]);
    }

    private function send(Client $client, array $messages)
    {
        return $client->messages->create(
            model: 'claude-opus-5',
            maxTokens: 4096,
            system: 'You are the AI assistant embedded in LaunchHR, an HR platform. '
                .'You can look up and manage the acting employee\'s time off (holidays, sick days, PTO), '
                .'and, for HR/admins, the company-wide absence report. '
                .'Only act within the tools you\'re given - never claim to have done something you didn\'t call a tool for. '
                .'Keep responses concise and conversational.',
            tools: AiToolRegistry::definitions(),
            messages: $messages,
        );
    }
}
