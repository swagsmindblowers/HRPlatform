<?php

namespace Tests\Unit\BetterOff;

use PHPUnit\Framework\TestCase;
use App\BetterOff\Engine\Dto\EmployerHireInput;
use App\BetterOff\Engine\EmployerCostCalculator;
use Tests\Support\BetterOff\InMemoryRatesRepository;

/**
 * The six engine test cases required by the BetterOff.FYI scope of work
 * (Workstream 4), verbatim. This suite needs no database — the calculator
 * is pure and takes its rates through InMemoryRatesRepository.
 */
class EmployerCostCalculatorTest extends TestCase
{
    private function baseRates(): InMemoryRatesRepository
    {
        return (new InMemoryRatesRepository())
            ->withRate('uk.employer_ni.rate', 0.138, '2020-01-01')
            ->withRate('uk.employer_ni.threshold', 9100, '2020-01-01')
            ->withRate('uk.pension.employer_min_rate', 0.03, '2020-01-01')
            ->withRate('uk.sponsorship.cos_fee', 239, '2020-01-01')
            ->withRate('uk.sponsorship.skills_charge.small_employer_per_year', 364, '2020-01-01', '2023-03-31')
            ->withRate('uk.sponsorship.skills_charge.small_employer_per_year', 480, '2023-04-01')
            ->withRate('uk.sponsorship.skills_charge.large_employer_per_year', 1000, '2020-01-01')
            ->withRate('uk.visa.skilled_worker.application_fee', 719, '2020-01-01')
            ->withRate('uk.visa.skilled_worker.ihs_per_year', 1035, '2020-01-01')
            ->withRate('uk.sponsorship.licence_fee.small_employer', 536, '2020-01-01')
            ->withRate('uk.sponsorship.licence_fee.large_employer', 1476, '2020-01-01')
            ->withRate('uk.skilled_worker.going_rate.2136', 34000, '2020-01-01');
    }

    /** Case 1: a UK hire in the same city shows zero on every sponsorship line. */
    public function test_uk_hire_has_zero_on_every_sponsorship_line(): void
    {
        $calculator = new EmployerCostCalculator($this->baseRates());

        $result = $calculator->calculate(new EmployerHireInput(
            hireType: EmployerHireInput::HIRE_TYPE_UK,
            annualSalary: 50000,
            employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_SMALL,
            hasSponsorLicence: false,
            asOfDate: new \DateTimeImmutable('2024-06-01'),
        ));

        $sponsorshipKeys = [
            'certificate_of_sponsorship_fee',
            'immigration_skills_charge',
            'visa_fees_and_health_surcharge',
            'sponsor_licence_fee',
        ];
        foreach ($result->lineItems as $item) {
            if (in_array($item->key, $sponsorshipKeys, true)) {
                $this->assertSame(0.0, $item->amount, "{$item->key} should be zero for a UK hire");
            }
        }
    }

    /** Case 2: a sponsored hire with an existing licence omits the licence fee. */
    public function test_sponsored_hire_with_existing_licence_omits_licence_fee(): void
    {
        $calculator = new EmployerCostCalculator($this->baseRates());

        $result = $calculator->calculate(new EmployerHireInput(
            hireType: EmployerHireInput::HIRE_TYPE_SPONSORED,
            annualSalary: 50000,
            employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_SMALL,
            hasSponsorLicence: true,
            asOfDate: new \DateTimeImmutable('2024-06-01'),
        ));

        $licenceLine = current(array_filter($result->lineItems, fn ($i) => $i->key === 'sponsor_licence_fee'));
        $this->assertNotFalse($licenceLine);
        $this->assertSame(0.0, $licenceLine->amount);
        $this->assertEmpty(array_filter($result->warnings, fn ($w) => str_contains($w, 'no sponsor licence')));
    }

    /** Case 3: a sponsored hire with no licence adds the licence fee and a lead-time warning. */
    public function test_sponsored_hire_with_no_licence_adds_fee_and_lead_time_warning(): void
    {
        $calculator = new EmployerCostCalculator($this->baseRates());

        $result = $calculator->calculate(new EmployerHireInput(
            hireType: EmployerHireInput::HIRE_TYPE_SPONSORED,
            annualSalary: 50000,
            employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_SMALL,
            hasSponsorLicence: false,
            asOfDate: new \DateTimeImmutable('2024-06-01'),
        ));

        $licenceLine = current(array_filter($result->lineItems, fn ($i) => $i->key === 'sponsor_licence_fee'));
        $this->assertSame(536.0, $licenceLine->amount);
        $this->assertNotEmpty(array_filter($result->warnings, fn ($w) => str_contains($w, 'lead time')));
    }

    /** Case 4: a salary below the going rate for the chosen SOC code raises an indicative warning, never a pass or fail. */
    public function test_salary_below_going_rate_raises_indicative_warning_only(): void
    {
        $calculator = new EmployerCostCalculator($this->baseRates());

        $result = $calculator->calculate(new EmployerHireInput(
            hireType: EmployerHireInput::HIRE_TYPE_SPONSORED,
            annualSalary: 20000, // below the 34000 going rate fixture
            employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_SMALL,
            hasSponsorLicence: true,
            asOfDate: new \DateTimeImmutable('2024-06-01'),
            socCode: '2136',
        ));

        $this->assertNotEmpty(array_filter($result->warnings, fn ($w) => str_contains($w, 'not an eligibility assessment')));
        // The result never exposes a pass/fail verdict — only line items, a total, and warnings.
        $this->assertIsFloat($result->totalCost);
        $this->assertGreaterThan(0, $result->totalCost);
    }

    /** Case 5: an as-at date before a rate change returns the earlier rate. */
    public function test_as_of_date_before_rate_change_returns_earlier_rate(): void
    {
        $calculator = new EmployerCostCalculator($this->baseRates());

        $before = $calculator->calculate(new EmployerHireInput(
            hireType: EmployerHireInput::HIRE_TYPE_SPONSORED,
            annualSalary: 50000,
            employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_SMALL,
            hasSponsorLicence: true,
            asOfDate: new \DateTimeImmutable('2022-01-01'), // before the 2023-04-01 change
        ));
        $after = $calculator->calculate(new EmployerHireInput(
            hireType: EmployerHireInput::HIRE_TYPE_SPONSORED,
            annualSalary: 50000,
            employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_SMALL,
            hasSponsorLicence: true,
            asOfDate: new \DateTimeImmutable('2024-06-01'),
        ));

        $iscBefore = current(array_filter($before->lineItems, fn ($i) => $i->key === 'immigration_skills_charge'));
        $iscAfter = current(array_filter($after->lineItems, fn ($i) => $i->key === 'immigration_skills_charge'));

        $this->assertSame(364.0 * 3, $iscBefore->amount); // default 3-year contract length, pre-change rate
        $this->assertSame(480.0 * 3, $iscAfter->amount); // post-change rate
    }

    /** Case 6: changing employer size changes the Immigration Skills Charge line. */
    public function test_changing_employer_size_changes_immigration_skills_charge(): void
    {
        $calculator = new EmployerCostCalculator($this->baseRates());
        $asOf = new \DateTimeImmutable('2024-06-01');

        $small = $calculator->calculate(new EmployerHireInput(
            hireType: EmployerHireInput::HIRE_TYPE_SPONSORED,
            annualSalary: 50000,
            employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_SMALL,
            hasSponsorLicence: true,
            asOfDate: $asOf,
        ));
        $large = $calculator->calculate(new EmployerHireInput(
            hireType: EmployerHireInput::HIRE_TYPE_SPONSORED,
            annualSalary: 50000,
            employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_LARGE,
            hasSponsorLicence: true,
            asOfDate: $asOf,
        ));

        $iscSmall = current(array_filter($small->lineItems, fn ($i) => $i->key === 'immigration_skills_charge'));
        $iscLarge = current(array_filter($large->lineItems, fn ($i) => $i->key === 'immigration_skills_charge'));

        $this->assertNotEquals($iscSmall->amount, $iscLarge->amount);
        $this->assertSame(480.0 * 3, $iscSmall->amount);
        $this->assertSame(1000.0 * 3, $iscLarge->amount);
    }
}
