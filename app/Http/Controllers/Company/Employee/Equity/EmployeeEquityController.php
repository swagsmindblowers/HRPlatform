<?php

namespace App\Http\Controllers\Company\Employee\Equity;

use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use App\Models\Company\Employee;
use App\Helpers\PermissionHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\ViewHelpers\Employee\EmployeeEquityViewHelper;
use App\Services\Company\Employee\Equity\CreateEquityGrant;
use App\Services\Company\Employee\Equity\DestroyEquityGrant;

class EmployeeEquityController extends Controller
{
    /**
     * Get the equity grants for the given employee.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @return JsonResponse
     */
    public function index(Request $request, int $companyId, int $employeeId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();
        $employee = Employee::where('company_id', $companyId)->findOrFail($employeeId);

        $permissions = PermissionHelper::permissions($loggedEmployee, $employee);
        if (! $permissions['can_see_equity']) {
            abort(403);
        }

        return response()->json([
            'data' => EmployeeEquityViewHelper::grants($employee),
        ], 200);
    }

    /**
     * Store a new equity grant.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @return JsonResponse
     */
    public function store(Request $request, int $companyId, int $employeeId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        $data = [
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'employee_id' => $employeeId,
            'grant_type' => $request->input('grant_type'),
            'units' => $request->input('units'),
            'strike_price' => $request->input('strike_price'),
            'grant_date' => $request->input('grant_date'),
            'vesting_start_date' => $request->input('vesting_start_date'),
            'cliff_months' => $request->input('cliff_months', 12),
            'vesting_months' => $request->input('vesting_months', 48),
            'notes' => $request->input('notes'),
        ];

        (new CreateEquityGrant)->execute($data);

        $employee = Employee::where('company_id', $companyId)->findOrFail($employeeId);

        return response()->json([
            'data' => EmployeeEquityViewHelper::grants($employee),
        ], 201);
    }

    /**
     * Destroy an equity grant.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @param int $equityGrantId
     * @return JsonResponse
     */
    public function destroy(Request $request, int $companyId, int $employeeId, int $equityGrantId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        (new DestroyEquityGrant)->execute([
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'employee_id' => $employeeId,
            'equity_grant_id' => $equityGrantId,
        ]);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
