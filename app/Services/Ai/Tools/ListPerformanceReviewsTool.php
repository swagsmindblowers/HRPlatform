<?php

namespace App\Services\Ai\Tools;

use Exception;
use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Http\ViewHelpers\Employee\EmployeePerformanceViewHelper;

class ListPerformanceReviewsTool implements AiTool
{
    public static function name(): string
    {
        return 'list_performance_reviews';
    }

    public static function description(): string
    {
        return 'List performance reviews for the acting employee, or for another employee by id if the acting employee is HR/admin or their manager.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'employee_id' => [
                    'type' => 'integer',
                    'description' => 'Optional employee id to look up instead of the acting employee.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        $employee = $actingEmployee;

        if (! empty($input['employee_id']) && (int) $input['employee_id'] !== $actingEmployee->id) {
            $isManager = $actingEmployee->isManagerOf((int) $input['employee_id']);
            if ($actingEmployee->permission_level > 200 && ! $isManager) {
                throw new Exception('Only HR/admin or the employee\'s manager can view another employee\'s performance reviews.');
            }

            $employee = Employee::where('company_id', $company->id)->findOrFail($input['employee_id']);
        }

        return [
            'reviews' => EmployeePerformanceViewHelper::performanceReviews($employee),
        ];
    }
}
