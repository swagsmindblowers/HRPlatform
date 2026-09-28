<?php

namespace App\Services\BetterOff;

use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use App\Models\BetterOff\Scenario;
use App\Models\BetterOff\ScenarioEvent;
use App\BetterOff\Rates\RatesRepositoryInterface;
use App\BetterOff\Engine\CandidateTakeHomeCalculator;
use App\BetterOff\Engine\Dto\CandidateRelocationInput;

/**
 * Persists a scenario as an immutable snapshot: the inputs, the as-at date,
 * and the rates-table version tag that produced it, so a saved figure can
 * always be reproduced even after the rates table changes later.
 * HR-and-above only, matching the existing compliance-item permission gate.
 */
class SaveScenario extends BaseService
{
    public function __construct(private readonly RatesRepositoryInterface $rates)
    {
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'author_id' => 'required|integer|exists:employees,id',
            'job_opening_id' => 'nullable|integer|exists:job_openings,id',
            'candidate_id' => 'nullable|integer|exists:candidates,id',
            'hire_type' => 'required|in:uk,sponsored',
            'as_of_date' => 'required|date_format:Y-m-d',
            'employer_result' => 'required|array',
            'employer_inputs' => 'required|array',
            'candidate_inputs' => 'nullable|array',
        ];
    }

    public function execute(array $data): Scenario
    {
        $this->validateRules($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canExecuteService();

        $candidateResult = null;
        if (! empty($data['candidate_inputs'])) {
            $calculator = new CandidateTakeHomeCalculator($this->rates);
            $ci = $data['candidate_inputs'];
            $result = $calculator->calculate(new CandidateRelocationInput(
                currentAnnualSalary: (float) $ci['current_annual_salary'],
                currentLocationCostIndexKey: $ci['current_location_cost_index_key'],
                newAnnualSalary: (float) $ci['new_annual_salary'],
                newLocationCostIndexKey: $ci['new_location_cost_index_key'],
                asOfDate: Carbon::parse($data['as_of_date']),
            ));
            $candidateResult = $result->toArray();
        }

        $scenario = Scenario::create([
            'company_id' => $data['company_id'],
            'job_opening_id' => $data['job_opening_id'] ?? null,
            'candidate_id' => $data['candidate_id'] ?? null,
            'created_by_user_id' => $this->author->user_id ?? null,
            'hire_type' => $data['hire_type'],
            'as_of_date' => $data['as_of_date'],
            'rates_version_tag' => $this->rates->currentVersionTag(),
            'inputs' => [
                'employer' => $data['employer_inputs'],
                'candidate' => $data['candidate_inputs'] ?? null,
            ],
            'employer_result' => $data['employer_result'],
            'candidate_result' => $candidateResult,
        ]);

        ScenarioEvent::create([
            'scenario_id' => $scenario->id,
            'user_id' => $this->author->user_id ?? null,
            'action' => ScenarioEvent::ACTION_SAVED,
        ]);

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'betteroff_scenario_saved',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'scenario_id' => $scenario->id,
                'hire_type' => $scenario->hire_type,
            ]),
        ])->onQueue('low');

        return $scenario;
    }
}
