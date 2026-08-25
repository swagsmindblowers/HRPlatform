<?php

namespace App\Http\Controllers\Company\Employee\Onboarding;

use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use App\Models\Company\Employee;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\ViewHelpers\Employee\EmployeeOnboardingViewHelper;
use App\Services\Company\Employee\Onboarding\CompleteOnboardingChecklistItem;
use App\Services\Company\Employee\Onboarding\CreateOnboardingChecklistForEmployee;

class EmployeeOnboardingController extends Controller
{
    /**
     * Get the onboarding checklist for the given employee.
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
            'data' => EmployeeOnboardingViewHelper::checklist($employee),
        ], 200);
    }

    /**
     * Start an onboarding checklist for the given employee.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @return JsonResponse
     */
    public function store(Request $request, int $companyId, int $employeeId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        (new CreateOnboardingChecklistForEmployee)->execute([
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'employee_id' => $employeeId,
            'jurisdiction' => $request->input('jurisdiction'),
            'is_contractor' => $request->input('is_contractor', false),
        ]);

        $employee = Employee::where('company_id', $companyId)->findOrFail($employeeId);

        return response()->json([
            'data' => EmployeeOnboardingViewHelper::checklist($employee),
        ], 201);
    }

    /**
     * Mark a checklist item as complete.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @param int $itemId
     * @return JsonResponse
     */
    public function completeItem(Request $request, int $companyId, int $employeeId, int $itemId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        (new CompleteOnboardingChecklistItem)->execute([
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'employee_id' => $employeeId,
            'checklist_item_id' => $itemId,
        ]);

        $employee = Employee::where('company_id', $companyId)->findOrFail($employeeId);

        return response()->json([
            'data' => EmployeeOnboardingViewHelper::checklist($employee),
        ], 200);
    }
}
