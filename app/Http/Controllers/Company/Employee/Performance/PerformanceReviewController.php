<?php

namespace App\Http\Controllers\Company\Employee\Performance;

use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use App\Models\Company\Employee;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\ViewHelpers\Employee\EmployeePerformanceViewHelper;
use App\Services\Company\Employee\PerformanceReview\CreatePerformanceReview;
use App\Services\Company\Employee\PerformanceReview\UpdatePerformanceReview;

class PerformanceReviewController extends Controller
{
    /**
     * Get all performance reviews for the given employee.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @return JsonResponse
     */
    public function index(Request $request, int $companyId, int $employeeId): JsonResponse
    {
        $employee = Employee::where('company_id', $companyId)->findOrFail($employeeId);

        return response()->json([
            'data' => EmployeePerformanceViewHelper::performanceReviews($employee),
        ], 200);
    }

    /**
     * Create a performance review for the given employee.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @return JsonResponse
     */
    public function store(Request $request, int $companyId, int $employeeId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        (new CreatePerformanceReview)->execute([
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'employee_id' => $employeeId,
            'review_type' => $request->input('review_type'),
            'review_date' => $request->input('review_date'),
            'rating' => $request->input('rating'),
            'strengths' => $request->input('strengths'),
            'areas_for_improvement' => $request->input('areas_for_improvement'),
            'notes' => $request->input('notes'),
            'status' => $request->input('status'),
        ]);

        $employee = Employee::where('company_id', $companyId)->findOrFail($employeeId);

        return response()->json([
            'data' => EmployeePerformanceViewHelper::performanceReviews($employee),
        ], 201);
    }

    /**
     * Update a performance review.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @param int $reviewId
     * @return JsonResponse
     */
    public function update(Request $request, int $companyId, int $employeeId, int $reviewId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        (new UpdatePerformanceReview)->execute([
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'employee_id' => $employeeId,
            'performance_review_id' => $reviewId,
            'rating' => $request->input('rating'),
            'strengths' => $request->input('strengths'),
            'areas_for_improvement' => $request->input('areas_for_improvement'),
            'notes' => $request->input('notes'),
            'status' => $request->input('status'),
        ]);

        $employee = Employee::where('company_id', $companyId)->findOrFail($employeeId);

        return response()->json([
            'data' => EmployeePerformanceViewHelper::performanceReviews($employee),
        ], 200);
    }
}
