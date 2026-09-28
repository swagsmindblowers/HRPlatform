<?php

namespace App\Http\Controllers\Company\Employee;

use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Company\Employee\SetEmploymentType;

class EmployeeEmploymentTypeController extends Controller
{
    /**
     * Set the employment type (employee/contractor) for the given employee.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @return JsonResponse
     */
    public function store(Request $request, int $companyId, int $employeeId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        $employee = (new SetEmploymentType)->execute([
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'employee_id' => $employeeId,
            'employment_type' => $request->input('employment_type'),
        ]);

        return response()->json([
            'data' => [
                'id' => $employee->id,
                'employment_type' => $employee->employment_type,
                'is_contractor' => $employee->isContractor(),
            ],
        ], 200);
    }
}
