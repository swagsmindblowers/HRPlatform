<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Http\ViewHelpers\Employee\EmployeeOnboardingViewHelper;

class GetOnboardingChecklistTool implements AiTool
{
    public static function name(): string
    {
        return 'get_onboarding_checklist';
    }

    public static function description(): string
    {
        return 'Get the onboarding checklist and compliance deadlines for the acting employee, or for another employee by id if the acting employee is HR/admin.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'employee_id' => [
                    'type' => 'integer',
                    'description' => 'Optional employee id to look up instead of the acting employee. Requires HR/admin permission.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        $employee = $actingEmployee;

        if (! empty($input['employee_id']) && (int) $input['employee_id'] !== $actingEmployee->id) {
            if ($actingEmployee->permission_level > 200) {
                throw new \Exception('Only HR/admin can view another employee\'s onboarding checklist.');
            }

            $employee = Employee::where('company_id', $company->id)->findOrFail($input['employee_id']);
        }

        return [
            'checklist' => EmployeeOnboardingViewHelper::checklist($employee),
        ];
    }
}
