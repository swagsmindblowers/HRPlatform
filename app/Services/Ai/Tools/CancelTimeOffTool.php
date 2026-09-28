<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Services\Company\Employee\Holiday\DestroyTimeOff;

class CancelTimeOffTool implements AiTool
{
    public static function name(): string
    {
        return 'cancel_time_off';
    }

    public static function description(): string
    {
        return 'Cancel a previously logged day off, given its id (from list_my_time_off).';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'time_off_id' => [
                    'type' => 'integer',
                    'description' => 'The id of the planned time off to cancel.',
                ],
            ],
            'required' => ['time_off_id'],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        (new DestroyTimeOff)->execute([
            'company_id' => $company->id,
            'author_id' => $actingEmployee->id,
            'employee_id' => $actingEmployee->id,
            'employee_planned_holiday_id' => $input['time_off_id'],
        ]);

        return ['cancelled' => true, 'time_off_id' => $input['time_off_id']];
    }
}
