<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use Illuminate\Http\JsonResponse;
use App\Services\Ai\AiToolRegistry;
use App\Http\Controllers\Controller;

/**
 * HTTP surface the standalone MCP server calls into. Authenticated via a
 * Sanctum personal access token; the `company` route middleware (shared with
 * the web routes) resolves the acting employee/company from the token's
 * owning user and caches them exactly like a normal session request.
 */
class AiToolsController extends Controller
{
    /**
     * List the tools available, so the MCP server can register them
     * dynamically instead of hardcoding a duplicate tool list.
     *
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     */
    public function index(Request $request, int $companyId): JsonResponse
    {
        if (! $this->tokenCanUseAiTools($request)) {
            return response()->json(['error' => 'This token isn\'t authorized to use the AI tools.'], 403);
        }

        return response()->json([
            'data' => AiToolRegistry::jsonDefinitions(),
        ]);
    }

    /**
     * Invoke a single tool by name.
     *
     * @param Request $request
     * @param int $companyId
     * @param string $tool
     * @return JsonResponse
     */
    public function invoke(Request $request, int $companyId, string $tool): JsonResponse
    {
        if (! $this->tokenCanUseAiTools($request)) {
            return response()->json(['error' => 'This token isn\'t authorized to use the AI tools.'], 403);
        }

        $company = InstanceHelper::getLoggedCompany();
        $employee = InstanceHelper::getLoggedEmployee();

        try {
            $result = AiToolRegistry::dispatch($tool, $request->input('input', []), $employee, $company);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'data' => $result,
        ]);
    }

    /**
     * Personal access tokens created for the MCP integration are scoped
     * with the 'ai-tools' ability - reject anything else (e.g. a token
     * created for a different purpose in the future).
     */
    private function tokenCanUseAiTools(Request $request): bool
    {
        $token = $request->user()->currentAccessToken();

        return $token && $token->can('ai-tools');
    }
}
