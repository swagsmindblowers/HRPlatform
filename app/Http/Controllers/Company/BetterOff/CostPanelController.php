<?php

namespace App\Http\Controllers\Company\BetterOff;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use Illuminate\Http\JsonResponse;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Company\JobOpening;
use App\Models\BetterOff\Scenario;
use App\Services\BetterOff\SaveScenario;
use App\Services\BetterOff\ShareScenario;
use App\Services\BetterOff\CalculateEmployerCost;

/**
 * The cost panel attached to a role/offer: prefills what the role record can
 * (currently just the role's title — see docs/betteroff/architecture-audit.md,
 * the role chain has no salary/location field, so salary is always entered by
 * the user, matching the scope of work's own spec), lets the user compare a
 * UK hire against a sponsored hire, and save the scenario.
 */
class CostPanelController extends Controller
{
    public function index(Request $request, int $companyId, ?int $jobOpeningId = null): Response
    {
        $company = InstanceHelper::getLoggedCompany();
        $jobOpening = $jobOpeningId ? JobOpening::where('company_id', $companyId)->find($jobOpeningId) : null;

        return Inertia::render('BetterOff/CostPanel', [
            'notifications' => NotificationHelper::getNotifications(InstanceHelper::getLoggedEmployee()),
            'jobOpening' => $jobOpening,
            'companySettings' => [
                'hasSponsorLicence' => (bool) $company->betteroff_has_sponsor_licence,
                'employerSizeClass' => $company->betteroff_employer_size_class,
                'defaultEmployerVisaSharePercent' => $company->betteroff_default_employer_visa_share_percent,
            ],
        ]);
    }

    /**
     * Live, unsaved calculation — called as the user adjusts inputs.
     */
    public function calculate(Request $request, CalculateEmployerCost $service): JsonResponse
    {
        $result = $service->execute($request->all());

        return response()->json($result);
    }

    /**
     * Persist the current scenario as an immutable snapshot.
     */
    public function store(Request $request, int $companyId, SaveScenario $service): JsonResponse
    {
        $employee = InstanceHelper::getLoggedEmployee();

        $scenario = $service->execute(array_merge($request->all(), [
            'company_id' => $companyId,
            'author_id' => $employee->id,
        ]));

        return response()->json(['data' => $scenario], 201);
    }

    public function share(Request $request, int $companyId, int $scenarioId, ShareScenario $service): JsonResponse
    {
        $employee = InstanceHelper::getLoggedEmployee();

        $scenario = $service->execute([
            'company_id' => $companyId,
            'author_id' => $employee->id,
            'scenario_id' => $scenarioId,
        ]);

        return response()->json([
            'share_url' => route('betteroff.candidate-share', ['token' => $scenario->share_token]),
            'expires_at' => $scenario->share_expires_at,
        ]);
    }
}
