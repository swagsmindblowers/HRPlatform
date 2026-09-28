<?php

namespace App\Http\Controllers\Company\Hub;

use Inertia\Inertia;
use Inertia\Response;
use App\Helpers\InstanceHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;

class ComplianceHubController extends Controller
{
    /**
     * The "Compliance" hub, promoted out of the generic Adminland catch-all
     * into its own first-class, admin-only destination.
     *
     * @return Response
     */
    public function index(): Response
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        return Inertia::render('Hub/Compliance', [
            'notifications' => NotificationHelper::getNotifications($loggedEmployee),
        ]);
    }
}
