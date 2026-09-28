<?php

namespace App\Http\Controllers\BetterOff;

use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\BetterOff\Scenario;
use App\BetterOff\Rates\RatesRepositoryInterface;
use App\BetterOff\Engine\CandidateTakeHomeCalculator;
use App\BetterOff\Engine\Dto\CandidateRelocationInput;
use App\Services\BetterOff\CalculateEmployerCost;

/**
 * The public, no-login BetterOff.FYI calculator. Reuses the exact same
 * engine and rates repository as the in-app cost panel — same as-at-date
 * and source display, no separate calculation path to drift out of sync.
 *
 * Collects no employee-level personal data: every input here is role-level
 * (salary, location, hire type), never a named person's details.
 */
class PublicCalculatorController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('BetterOff/PublicCalculator');
    }

    public function calculate(Request $request, CalculateEmployerCost $employerService): JsonResponse
    {
        $result = $employerService->execute($request->all());

        return response()->json($result);
    }

    public function calculateCandidate(Request $request, RatesRepositoryInterface $rates): JsonResponse
    {
        $calculator = new CandidateTakeHomeCalculator($rates);
        $asOfDate = Carbon::parse($request->input('as_of_date', now()->toDateString()));

        $result = $calculator->calculate(new CandidateRelocationInput(
            currentAnnualSalary: (float) $request->input('current_annual_salary'),
            currentLocationCostIndexKey: $request->input('current_location_cost_index_key'),
            newAnnualSalary: (float) $request->input('new_annual_salary'),
            newLocationCostIndexKey: $request->input('new_location_cost_index_key'),
            asOfDate: $asOfDate,
        ));

        return response()->json(['result' => $result->toArray()]);
    }

    /**
     * Shareable result: reproduces a saved scenario deterministically from
     * its stored inputs and as-at date. No login required, no employer cost
     * lines are exposed — only whatever was saved as the candidate_result.
     */
    public function sharedCandidateView(string $token): Response
    {
        $scenario = Scenario::where('share_token', $token)
            ->where('share_expires_at', '>=', now())
            ->firstOrFail();

        return Inertia::render('BetterOff/CandidateShare', [
            'result' => $scenario->candidate_result,
            'asOfDate' => $scenario->as_of_date->format('Y-m-d'),
            'ratesVersionTag' => $scenario->rates_version_tag,
        ]);
    }
}
