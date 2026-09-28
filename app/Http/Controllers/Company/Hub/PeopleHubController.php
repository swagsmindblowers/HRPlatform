<?php

namespace App\Http\Controllers\Company\Hub;

use Inertia\Inertia;
use Inertia\Response;
use App\Helpers\InstanceHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;

class PeopleHubController extends Controller
{
    /**
     * The "People" hub: directory, org chart, and teams in one place,
     * instead of scattered under a generic "Company" nav item.
     *
     * @return Response
     */
    public function index(): Response
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        return Inertia::render('Hub/People', [
            'notifications' => NotificationHelper::getNotifications($loggedEmployee),
            'permissions' => [
                'is_hr_or_admin' => $loggedEmployee->permission_level <= config('officelife.permission_level.hr'),
            ],
        ]);
    }
}
