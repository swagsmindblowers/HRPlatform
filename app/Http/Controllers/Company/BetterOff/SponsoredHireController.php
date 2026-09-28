<?php

namespace App\Http\Controllers\Company\BetterOff;

use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\BetterOff\ConfirmSponsoredHire;

class SponsoredHireController extends Controller
{
    /**
     * Confirm a sponsored hire from a saved scenario. Generates the
     * right-to-work-check, certificate-of-sponsorship, and reporting-deadline
     * tasks, mirrored into the company's existing compliance checklist — the
     * user re-enters nothing.
     */
    public function store(Request $request, int $companyId, ConfirmSponsoredHire $service): JsonResponse
    {
        $employee = InstanceHelper::getLoggedEmployee();

        $sponsoredWorker = $service->execute(array_merge($request->all(), [
            'company_id' => $companyId,
            'author_id' => $employee->id,
        ]));

        return response()->json(['data' => $sponsoredWorker], 201);
    }
}
