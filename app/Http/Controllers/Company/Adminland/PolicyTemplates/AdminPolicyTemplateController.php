<?php

namespace App\Http\Controllers\Company\Adminland\PolicyTemplates;

use Inertia\Inertia;
use Inertia\Response;
use App\Helpers\InstanceHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Http\ViewHelpers\Adminland\AdminComplianceViewHelper;

class AdminPolicyTemplateController extends Controller
{
    /**
     * Show the policy template library.
     *
     * @return Response
     */
    public function index(): Response
    {
        $company = InstanceHelper::getLoggedCompany();

        return Inertia::render('Adminland/PolicyTemplates/Index', [
            'notifications' => NotificationHelper::getNotifications(InstanceHelper::getLoggedEmployee()),
            'templates' => AdminComplianceViewHelper::policyTemplates($company),
        ]);
    }
}
