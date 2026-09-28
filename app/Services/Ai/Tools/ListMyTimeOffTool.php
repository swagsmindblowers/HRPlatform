<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;

class ListMyTimeOffTool implements AiTool
{
    public static function name(): string
    {
        return 'list_my_time_off';
    }

    public static function description(): string
    {
        return 'List the acting employee\'s holiday balance, upcoming time off, and recently taken time off.';
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
        return $actingEmployee->getHolidaysInformation();
    }
}
