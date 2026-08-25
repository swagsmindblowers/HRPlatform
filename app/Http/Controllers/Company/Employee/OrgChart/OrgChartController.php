<?php

namespace App\Http\Controllers\Company\Employee\OrgChart;

use Inertia\Inertia;
use App\Helpers\InstanceHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Http\ViewHelpers\Employee\OrgChartViewHelper;

class OrgChartController extends Controller
{
    /**
     * Display the org chart for the company.
     *
     * @return mixed
     */
    public function index()
    {
        $company = InstanceHelper::getLoggedCompany();
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        return Inertia::render('Employee/OrgChart/Index', [
            'notifications' => NotificationHelper::getNotifications($loggedEmployee),
            'tree' => OrgChartViewHelper::tree($company),
        ]);
    }
}
