<?php

namespace App\Services\BetterOff;

use Carbon\Carbon;
use App\BetterOff\Rates\RatesRepositoryInterface;
use App\BetterOff\Engine\EmployerCostCalculator;
use App\BetterOff\Engine\Dto\EmployerHireInput;

/**
 * Stateless calculation used by the cost panel while the user is still
 * adjusting inputs — no database write. Saving a scenario is a separate,
 * explicit step (SaveScenario) so the calculator can stay responsive without
 * writing a row on every keystroke.
 */
class CalculateEmployerCost
{
    public function __construct(private readonly RatesRepositoryInterface $rates)
    {
    }

    public function execute(array $data): array
    {
        $calculator = new EmployerCostCalculator($this->rates);
        $asOfDate = Carbon::parse($data['as_of_date'] ?? now()->toDateString());

        $input = new EmployerHireInput(
            hireType: $data['hire_type'],
            annualSalary: (float) $data['annual_salary'],
            employerSizeClass: $data['employer_size_class'] ?? EmployerHireInput::EMPLOYER_SIZE_SMALL,
            hasSponsorLicence: (bool) ($data['has_sponsor_licence'] ?? false),
            asOfDate: $asOfDate,
            socCode: $data['soc_code'] ?? null,
            contractLengthYears: (int) ($data['contract_length_years'] ?? 3),
            employerVisaCostSharePercent: (float) ($data['employer_visa_cost_share_percent'] ?? 100.0),
            includeRelocation: (bool) ($data['include_relocation'] ?? false),
            relocationCostOverride: isset($data['relocation_cost_override']) ? (float) $data['relocation_cost_override'] : null,
            isNewEntrant: (bool) ($data['is_new_entrant'] ?? false),
            hasRelevantPhd: (bool) ($data['has_relevant_phd'] ?? false),
            hasStemPhd: (bool) ($data['has_stem_phd'] ?? false),
            isOnImmigrationSalaryList: (bool) ($data['is_on_immigration_salary_list'] ?? false),
        );

        $result = $calculator->calculate($input);

        return [
            'result' => $result->toArray(),
            'rates_version_tag' => $this->rates->currentVersionTag(),
        ];
    }
}
