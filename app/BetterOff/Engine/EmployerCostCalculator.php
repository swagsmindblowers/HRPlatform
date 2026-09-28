<?php

namespace App\BetterOff\Engine;

use App\BetterOff\Rates\RatesRepositoryInterface;
use App\BetterOff\Engine\Dto\LineItem;
use App\BetterOff\Engine\Dto\EmployerHireInput;
use App\BetterOff\Engine\Dto\EmployerCostResult;

/**
 * Pure, deterministic: same EmployerHireInput + same rates-as-of-date always
 * produces the same EmployerCostResult. Every line item carries the rate key
 * it used, so the UI can show where each number came from.
 */
class EmployerCostCalculator
{
    public function __construct(private readonly RatesRepositoryInterface $rates)
    {
    }

    public function calculate(EmployerHireInput $input): EmployerCostResult
    {
        $warnings = [];
        $lineItems = [];

        $lineItems[] = new LineItem('salary', $input->annualSalary);

        $lineItems[] = $this->employerNiLineItem($input);
        $lineItems[] = $this->employerPensionLineItem($input);

        if ($input->hireType === EmployerHireInput::HIRE_TYPE_SPONSORED && $input->socCode !== null) {
            $goingRate = $this->rates->asOf('uk.skilled_worker.going_rate.'.$input->socCode, $input->asOfDate);
            if ($input->annualSalary < $goingRate->value) {
                $warnings[] = sprintf(
                    'Salary is below the indicative going rate for SOC code %s (%.2f GBP as of %s). '.
                    'This is a cost indicator only, not an eligibility assessment — it is never a pass or fail result.',
                    $input->socCode,
                    $goingRate->value,
                    $input->asOfDate->format('Y-m-d')
                );
            }
        }

        if ($input->hireType === EmployerHireInput::HIRE_TYPE_UK) {
            $lineItems[] = new LineItem('certificate_of_sponsorship_fee', 0.0, null, 'Not applicable for a UK hire.');
            $lineItems[] = new LineItem('immigration_skills_charge', 0.0, null, 'Not applicable for a UK hire.');
            $lineItems[] = new LineItem('visa_fees_and_health_surcharge', 0.0, null, 'Not applicable for a UK hire.');
            $lineItems[] = new LineItem('sponsor_licence_fee', 0.0, null, 'Not applicable for a UK hire.');
        } else {
            $cos = $this->rates->asOf('uk.sponsorship.cos_fee', $input->asOfDate);
            $lineItems[] = new LineItem('certificate_of_sponsorship_fee', $cos->value, $cos->key);

            $iscKey = $input->employerSizeClass === EmployerHireInput::EMPLOYER_SIZE_LARGE
                ? 'uk.sponsorship.skills_charge.large_employer_per_year'
                : 'uk.sponsorship.skills_charge.small_employer_per_year';
            $isc = $this->rates->asOf($iscKey, $input->asOfDate);
            $lineItems[] = new LineItem(
                'immigration_skills_charge',
                $isc->value * $input->contractLengthYears,
                $isc->key,
                sprintf('%d year(s) at the %s employer rate.', $input->contractLengthYears, $input->employerSizeClass)
            );

            $appFee = $this->rates->asOf('uk.visa.skilled_worker.application_fee', $input->asOfDate);
            $ihs = $this->rates->asOf('uk.visa.skilled_worker.ihs_per_year', $input->asOfDate);
            $employerShare = max(0.0, min(100.0, $input->employerVisaCostSharePercent)) / 100.0;
            $visaCost = ($appFee->value + ($ihs->value * $input->contractLengthYears)) * $employerShare;
            $lineItems[] = new LineItem(
                'visa_fees_and_health_surcharge',
                $visaCost,
                $appFee->key,
                sprintf('Employer pays %.0f%% of the application fee and %d year(s) of the health surcharge.', $employerShare * 100, $input->contractLengthYears)
            );

            if ($input->hasSponsorLicence) {
                $lineItems[] = new LineItem('sponsor_licence_fee', 0.0, null, 'Company already holds a sponsor licence.');
            } else {
                $licenceKey = $input->employerSizeClass === EmployerHireInput::EMPLOYER_SIZE_LARGE
                    ? 'uk.sponsorship.licence_fee.large_employer'
                    : 'uk.sponsorship.licence_fee.small_employer';
                $licence = $this->rates->asOf($licenceKey, $input->asOfDate);
                $lineItems[] = new LineItem('sponsor_licence_fee', $licence->value, $licence->key);
                $warnings[] = 'Company has no sponsor licence. A licence application is required before this hire can start '.
                    'and typically has a multi-week lead time — budget for that before setting a start date.';
            }
        }

        if ($input->includeRelocation) {
            if ($input->relocationCostOverride !== null) {
                $lineItems[] = new LineItem('relocation', $input->relocationCostOverride, null, 'Employer-entered relocation cost.');
            } else {
                $default = $this->rates->asOf('relocation.default_benchmark', $input->asOfDate);
                $lineItems[] = new LineItem('relocation', $default->value, $default->key, 'Estimate — no country-specific benchmark configured yet.');
            }
        }

        $total = array_sum(array_map(fn (LineItem $i) => $i->amount, $lineItems));

        return new EmployerCostResult($input->hireType, $lineItems, $total, $warnings, $input->asOfDate);
    }

    /**
     * Compare a UK hire against a sponsored overseas hire for the same role,
     * returning both results plus the difference (overseas total minus UK total).
     *
     * @return array{uk: EmployerCostResult, sponsored: EmployerCostResult, difference: float}
     */
    public function compareUkVsSponsored(EmployerHireInput $ukInput, EmployerHireInput $sponsoredInput): array
    {
        $uk = $this->calculate($ukInput);
        $sponsored = $this->calculate($sponsoredInput);

        return [
            'uk' => $uk,
            'sponsored' => $sponsored,
            'difference' => $sponsored->totalCost - $uk->totalCost,
        ];
    }

    private function employerNiLineItem(EmployerHireInput $input): LineItem
    {
        $rate = $this->rates->asOf('uk.employer_ni.rate', $input->asOfDate);
        $threshold = $this->rates->asOf('uk.employer_ni.threshold', $input->asOfDate);
        $amount = max(0.0, $input->annualSalary - $threshold->value) * $rate->value;

        return new LineItem('employer_ni', round($amount, 2), $rate->key);
    }

    private function employerPensionLineItem(EmployerHireInput $input): LineItem
    {
        $rate = $this->rates->asOf('uk.pension.employer_min_rate', $input->asOfDate);
        $amount = $input->annualSalary * $rate->value;

        return new LineItem('employer_pension', round($amount, 2), $rate->key);
    }
}
