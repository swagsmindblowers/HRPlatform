<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;

/**
 * Contract for a tool that can be called either by the in-app AI assistant
 * (Claude tool use, executed in-process) or, over HTTP via AiToolsController,
 * by the standalone MCP server.
 *
 * Every tool is a thin wrapper around an existing, already permission-checked
 * domain service - it must never touch the database directly for anything
 * that isn't a read-only lookup, so the AI can't do anything a user with the
 * acting employee's permissions couldn't already do through the normal UI.
 */
interface AiTool
{
    /**
     * Machine name used both as the Claude tool name and the MCP tool name.
     */
    public static function name(): string;

    /**
     * Human-readable description shown to the LLM.
     */
    public static function description(): string;

    /**
     * JSON schema (camelCase `inputSchema` shape, per the Anthropic PHP SDK)
     * describing the tool's parameters.
     */
    public static function schema(): array;

    /**
     * Run the tool for the given acting employee, scoped to their company.
     * Must throw an exception (caught by the caller) on any permission or
     * validation failure - never fail silently.
     *
     * @param array $input
     * @param Employee $actingEmployee
     * @param Company $company
     * @return array
     */
    public function execute(array $input, Employee $actingEmployee, Company $company): array;
}
