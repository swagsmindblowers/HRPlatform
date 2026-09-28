<?php

namespace App\Http\Controllers\Company\Hub;

use Inertia\Inertia;
use Inertia\Response;
use App\Helpers\InstanceHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;

class TimeAndPayHubController extends Controller
{
    /**
     * The "Time & Pay" hub - the answer to time off, timesheets, and
     * expenses previously being scattered across the generic Dashboard and
     * Adminland catch-alls instead of being a first-class destination. Also
     * where the payroll/accounting connectors panel lives, next to the
     * data it syncs.
     *
     * @return Response
     */
    public function index(): Response
    {
        $company = InstanceHelper::getLoggedCompany();
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        return Inertia::render('Hub/TimeAndPay', [
            'notifications' => NotificationHelper::getNotifications($loggedEmployee),
            'employee' => [
                'id' => $loggedEmployee->id,
            ],
            'permissions' => [
                'is_manager' => $loggedEmployee->getListOfDirectReports()->isNotEmpty(),
                'is_hr_or_admin' => $loggedEmployee->permission_level <= config('officelife.permission_level.hr'),
                'is_administrator' => $loggedEmployee->permission_level <= config('officelife.permission_level.administrator'),
            ],
        ]);
    }
}
