<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Personal access tokens the logged-in user creates for themselves, mainly
 * so they can connect the standalone MCP server to Claude Desktop/Code (or
 * any other MCP client) as themselves.
 */
class ApiTokenController extends Controller
{
    /**
     * List the user's tokens (never the plaintext value - only Sanctum's
     * metadata, since the value is only shown once at creation time).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->orderByDesc('created_at')->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'last_used_at' => optional($token->last_used_at)->diffForHumans(),
                'created_at' => $token->created_at->format('M j, Y'),
            ];
        });

        return response()->json(['data' => $tokens]);
    }

    /**
     * Create a new personal access token, scoped to the AI tools ability.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = $request->user()->createToken($request->input('name'), ['ai-tools']);

        return response()->json([
            'data' => [
                'plain_text_token' => $token->plainTextToken,
            ],
        ], 201);
    }

    /**
     * Revoke a personal access token.
     *
     * @param Request $request
     * @param int $tokenId
     * @return JsonResponse
     */
    public function destroy(Request $request, int $tokenId): JsonResponse
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return response()->json([], 200);
    }
}
