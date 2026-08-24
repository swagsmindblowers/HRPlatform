<?php

namespace App\Services\Ai;

use Exception;
use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Services\Ai\Tools\AiTool;
use App\Services\Ai\Tools\CancelTimeOffTool;
use App\Services\Ai\Tools\ListMyTimeOffTool;
use App\Services\Ai\Tools\RequestTimeOffTool;
use App\Services\Ai\Tools\SearchEmployeesTool;
use App\Services\Ai\Tools\GetAbsenceReportTool;

/**
 * Central registry of every tool the AI assistant and the MCP server can
 * call. Both surfaces go through this class so there is exactly one place
 * that lists what the AI is allowed to do.
 */
class AiToolRegistry
{
    /**
     * @return class-string<AiTool>[]
     */
    public static function toolClasses(): array
    {
        return [
            ListMyTimeOffTool::class,
            RequestTimeOffTool::class,
            CancelTimeOffTool::class,
            GetAbsenceReportTool::class,
            SearchEmployeesTool::class,
        ];
    }

    /**
     * Definitions in the shape the Anthropic PHP SDK expects (camelCase
     * `inputSchema`).
     */
    public static function definitions(): array
    {
        return collect(self::toolClasses())->map(fn ($class) => [
            'name' => $class::name(),
            'description' => $class::description(),
            'inputSchema' => $class::schema(),
        ])->values()->all();
    }

    /**
     * Definitions in a plain JSON shape for the MCP server (which doesn't
     * use the Anthropic SDK's camelCase convention).
     */
    public static function jsonDefinitions(): array
    {
        return collect(self::toolClasses())->map(fn ($class) => [
            'name' => $class::name(),
            'description' => $class::description(),
            'input_schema' => $class::schema(),
        ])->values()->all();
    }

    /**
     * Run a tool by name.
     *
     * @throws Exception if the tool doesn't exist, or the tool itself throws
     *                    on a permission/validation failure
     */
    public static function dispatch(string $name, array $input, Employee $actingEmployee, Company $company): array
    {
        foreach (self::toolClasses() as $class) {
            if ($class::name() === $name) {
                /** @var AiTool $tool */
                $tool = new $class;

                return $tool->execute($input, $actingEmployee, $company);
            }
        }

        throw new Exception("Unknown tool: {$name}");
    }
}
