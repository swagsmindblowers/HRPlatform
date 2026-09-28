<?php

namespace App\BetterOff\Engine;

use App\BetterOff\Rates\RatesRepositoryInterface;
use App\BetterOff\Engine\Dto\CandidateTakeHomeResult;
use App\BetterOff\Engine\Dto\CandidateRelocationInput;

class CandidateTakeHomeCalculator
{
    public function __construct(private readonly RatesRepositoryInterface $rates)
    {
    }

    public function calculate(CandidateRelocationInput $input): CandidateTakeHomeResult
    {
        $assumptions = [];

        $currentNet = $this->netAnnualIncome($input->currentAnnualSalary, $input->asOfDate, $assumptions);
        $newNet = $this->netAnnualIncome($input->newAnnualSalary, $input->asOfDate, $assumptions);

        $currentLiving = $this->rates->asOf($input->currentLocationCostIndexKey, $input->asOfDate);
        $newLiving = $this->rates->asOf($input->newLocationCostIndexKey, $input->asOfDate);
        $assumptions[] = sprintf(
            'Housing/living cost estimates are indicative (%s vs %s) — not a guarantee of actual cost of living.',
            $input->currentLocationCostIndexKey,
            $input->newLocationCostIndexKey
        );

        $currentMonthly = ($currentNet / 12) - $currentLiving->value;
        $newMonthly = ($newNet / 12) - $newLiving->value;

        return new CandidateTakeHomeResult(
            currentNetMonthly: round($currentMonthly, 2),
            newNetMonthly: round($newMonthly, 2),
            betterOffByPerMonth: round($newMonthly - $currentMonthly, 2),
            assumptions: $assumptions,
            asOfDate: $input->asOfDate,
        );
    }

    private function netAnnualIncome(float $grossSalary, \DateTimeInterface $asOfDate, array &$assumptions): float
    {
        $allowance = $this->rates->asOf('uk.income_tax.personal_allowance', $asOfDate);
        $basicRate = $this->rates->asOf('uk.income_tax.basic_rate', $asOfDate);
        $niRate = $this->rates->asOf('uk.employee_ni.rate', $asOfDate);
        $niThreshold = $this->rates->asOf('uk.employee_ni.threshold', $asOfDate);

        $taxable = max(0.0, $grossSalary - $allowance->value);
        $incomeTax = $taxable * $basicRate->value;

        $niable = max(0.0, $grossSalary - $niThreshold->value);
        $employeeNi = $niable * $niRate->value;

        $assumptions[] = sprintf(
            'Tax/NI calculated using basic-rate band only (%s, %s) — higher-rate band not modelled yet.',
            $basicRate->key,
            $niRate->key
        );

        return $grossSalary - $incomeTax - $employeeNi;
    }
}
