<?php

namespace App\Services\Ai\Tools;

use Exception;
use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Services\Company\Employee\Holiday\CreateTimeOff;

class RequestTimeOffTool implements AiTool
{
    public static function name(): string
    {
        return 'request_time_off';
    }

    public static function description(): string
    {
        return 'Log a day (or half day) off for the acting employee. HR/admins can log it for someone else by passing employee_name - the underlying permission check will reject this for anyone without HR rights.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'date' => [
                    'type' => 'string',
                    'description' => 'The date of the day off, in YYYY-MM-DD format.',
                ],
                'type' => [
                    'type' => 'string',
                    'enum' => ['holiday', 'sick', 'pto'],
                    'description' => 'The type of time off.',
                ],
                'full_day' => [
                    'type' => 'boolean',
                    'description' => 'True for a full day, false for a half day. Defaults to true.',
                ],
                'employee_name' => [
                    'type' => 'string',
                    'description' => 'Optional - the name of the employee to log this for, if not the acting employee. Requires HR/admin rights.',
                ],
            ],
            'required' => ['date', 'type'],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        $targetEmployee = $actingEmployee;

        if (! empty($input['employee_name'])) {
            $targetEmployee = ResolveEmployeeByName::forCompany($company, $input['employee_name']);
        }

        $plannedHoliday = (new CreateTimeOff)->execute([
            'company_id' => $company->id,
            'author_id' => $actingEmployee->id,
            'employee_id' => $targetEmployee->id,
            'date' => $input['date'],
            'type' => $input['type'],
            'full' => $input['full_day'] ?? true,
        ]);

        if (! $plannedHoliday) {
            throw new Exception('This day off couldn\'t be logged - it may already be fully booked.');
        }

        return [
            'id' => $plannedHoliday->id,
            'employee' => $targetEmployee->name,
            'date' => $input['date'],
            'type' => $input['type'],
            'full' => $input['full_day'] ?? true,
        ];
    }
}
