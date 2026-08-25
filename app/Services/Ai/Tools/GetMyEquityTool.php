<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Http\ViewHelpers\Employee\EmployeeEquityViewHelper;

class GetMyEquityTool implements AiTool
{
    public static function name(): string
    {
        return 'get_my_equity';
    }

    public static function description(): string
    {
        return 'Get the acting employee\'s own equity grants (options/RSUs) and vesting progress.';
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
            'grants' => EmployeeEquityViewHelper::grants($actingEmployee),
        ];
    }
}
