<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Http\ViewHelpers\Employee\EmployeePerformanceViewHelper;
use App\Services\Company\Employee\PerformanceReview\CreatePerformanceReview;

class CreatePerformanceReviewTool implements AiTool
{
    public static function name(): string
    {
        return 'create_performance_review';
    }

    public static function description(): string
    {
        return 'Create a performance review for an employee. HR/admin or the employee\'s manager only.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'employee_id' => ['type' => 'integer', 'description' => 'The employee the review is about.'],
                'review_type' => ['type' => 'string', 'enum' => ['30_day', '60_day', '90_day', 'ad_hoc']],
                'review_date' => ['type' => 'string', 'description' => 'Date in Y-m-d format.'],
                'rating' => ['type' => 'integer', 'description' => 'Optional rating from 1 to 5.'],
                'strengths' => ['type' => 'string'],
                'areas_for_improvement' => ['type' => 'string'],
                'notes' => ['type' => 'string'],
            ],
            'required' => ['employee_id', 'review_type', 'review_date'],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        (new CreatePerformanceReview)->execute([
            'company_id' => $company->id,
            'author_id' => $actingEmployee->id,
            'employee_id' => $input['employee_id'],
            'review_type' => $input['review_type'],
            'review_date' => $input['review_date'],
            'rating' => $input['rating'] ?? null,
            'strengths' => $input['strengths'] ?? null,
            'areas_for_improvement' => $input['areas_for_improvement'] ?? null,
            'notes' => $input['notes'] ?? null,
        ]);

        $employee = Employee::where('company_id', $company->id)->findOrFail($input['employee_id']);

        return [
            'reviews' => EmployeePerformanceViewHelper::performanceReviews($employee),
        ];
    }
}
