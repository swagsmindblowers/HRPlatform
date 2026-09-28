<?php

namespace Tests\Unit\BetterOff;

use PHPUnit\Framework\TestCase;
use App\BetterOff\Engine\CandidateTakeHomeCalculator;
use App\BetterOff\Engine\Dto\CandidateRelocationInput;
use Tests\Support\BetterOff\InMemoryRatesRepository;

class CandidateTakeHomeCalculatorTest extends TestCase
{
    public function test_it_computes_a_deterministic_better_off_by_figure_with_assumptions(): void
    {
        $rates = (new InMemoryRatesRepository())
            ->withRate('uk.income_tax.personal_allowance', 12570, '2020-01-01')
            ->withRate('uk.income_tax.basic_rate', 0.20, '2020-01-01')
            ->withRate('uk.employee_ni.rate', 0.08, '2020-01-01')
            ->withRate('uk.employee_ni.threshold', 12570, '2020-01-01')
            ->withRate('cost_of_living.uk.manchester', 1500, '2020-01-01')
            ->withRate('cost_of_living.uk.london', 2200, '2020-01-01');

        $calculator = new CandidateTakeHomeCalculator($rates);

        $result = $calculator->calculate(new CandidateRelocationInput(
            currentAnnualSalary: 45000,
            currentLocationCostIndexKey: 'cost_of_living.uk.manchester',
            newAnnualSalary: 60000,
            newLocationCostIndexKey: 'cost_of_living.uk.london',
            asOfDate: new \DateTimeImmutable('2024-06-01'),
        ));

        // Same inputs, same as-of date → same output (determinism check).
        $result2 = $calculator->calculate(new CandidateRelocationInput(
            currentAnnualSalary: 45000,
            currentLocationCostIndexKey: 'cost_of_living.uk.manchester',
            newAnnualSalary: 60000,
            newLocationCostIndexKey: 'cost_of_living.uk.london',
            asOfDate: new \DateTimeImmutable('2024-06-01'),
        ));

        $this->assertSame($result->betterOffByPerMonth, $result2->betterOffByPerMonth);
        $this->assertNotEmpty($result->assumptions);
    }
}
