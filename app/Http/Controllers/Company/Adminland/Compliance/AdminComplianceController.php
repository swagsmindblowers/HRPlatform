<?php

namespace App\Http\Controllers\Company\Adminland\Compliance;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use Illuminate\Http\JsonResponse;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Http\ViewHelpers\Adminland\AdminComplianceViewHelper;
use App\Services\Company\Adminland\Compliance\CreateComplianceItem;
use App\Services\Company\Adminland\Compliance\UpdateComplianceItemStatus;

class AdminComplianceController extends Controller
{
    /**
     * Show the compliance checklist page.
     *
     * @return Response
     */
    public function index(): Response
    {
        $company = InstanceHelper::getLoggedCompany();

        return Inertia::render('Adminland/Compliance/Index', [
            'notifications' => NotificationHelper::getNotifications(InstanceHelper::getLoggedEmployee()),
            'items' => AdminComplianceViewHelper::items($company),
        ]);
    }

    /**
     * Create a compliance item.
     *
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     */
    public function store(Request $request, int $companyId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        (new CreateComplianceItem)->execute([
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'jurisdiction' => $request->input('jurisdiction'),
            'due_date' => $request->input('due_date'),
            'notes' => $request->input('notes'),
        ]);

        $company = InstanceHelper::getLoggedCompany();

        return response()->json([
            'data' => AdminComplianceViewHelper::items($company),
        ], 201);
    }

    /**
     * Update the status of a compliance item.
     *
     * @param Request $request
     * @param int $companyId
     * @param int $itemId
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $companyId, int $itemId): JsonResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        (new UpdateComplianceItemStatus)->execute([
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'compliance_item_id' => $itemId,
            'status' => $request->input('status'),
        ]);

        $company = InstanceHelper::getLoggedCompany();

        return response()->json([
            'data' => AdminComplianceViewHelper::items($company),
        ], 200);
    }
}
