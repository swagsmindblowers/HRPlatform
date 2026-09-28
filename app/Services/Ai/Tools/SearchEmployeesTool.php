<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;

class SearchEmployeesTool implements AiTool
{
    public static function name(): string
    {
        return 'search_employees';
    }

    public static function description(): string
    {
        return 'Search employees in the company by name.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'description' => 'Full or partial name to search for.',
                ],
            ],
            'required' => ['name'],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        $matches = Employee::where('company_id', $company->id)
            ->where(function ($query) use ($input) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%'.$input['name'].'%'])
                    ->orWhere('first_name', 'like', '%'.$input['name'].'%')
                    ->orWhere('last_name', 'like', '%'.$input['name'].'%');
            })
            ->limit(10)
            ->get();

        return [
            'employees' => $matches->map(fn ($employee) => [
                'id' => $employee->id,
                'name' => $employee->name,
            ])->values(),
        ];
    }
}
