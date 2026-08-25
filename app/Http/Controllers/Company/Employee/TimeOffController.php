<?php

namespace App\Http\Controllers\Company\Employee;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Company\Employee\Holiday\CreateTimeOff;
use App\Services\Company\Employee\Holiday\DestroyTimeOff;

class TimeOffController extends Controller
{
    /**
     * Log a day (or half day) off for the given employee.
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
            'date' => $request->input('date'),
            'type' => $request->input('type'),
            'full' => $request->input('full'),
        ];

        try {
            $plannedHoliday = (new CreateTimeOff)->execute($data);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage() ?: 'This day off can\'t be logged.',
            ], 400);
        }

        return response()->json([
            'data' => $plannedHoliday,
        ], 201);
    }

    /**
     * Cancel a previously logged day off.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $employeeId
     * @param int $timeOffId
     * @return JsonResponse
     */
    public function destroy(Request $request, int $companyId, int $employeeId, int $timeOffId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        $data = [
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'employee_id' => $employeeId,
            'employee_planned_holiday_id' => $timeOffId,
        ];

        try {
            (new DestroyTimeOff)->execute($data);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage() ?: 'This day off can\'t be cancelled.',
            ], 400);
        }

        return response()->json([], 200);
    }
}
