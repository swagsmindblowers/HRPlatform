<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Http\ViewHelpers\Employee\OrgChartViewHelper;

class GetOrgChartTool implements AiTool
{
    public static function name(): string
    {
        return 'get_org_chart';
    }

    public static function description(): string
    {
        return 'Get the company\'s org chart as a tree of managers and direct reports.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => (object) [],
            'required' => [],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        return [
            'tree' => OrgChartViewHelper::tree($company),
        ];
    }
}
