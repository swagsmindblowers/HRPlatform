<?php

namespace App\Http\Controllers\BetterOff;

use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\BetterOff\Rates\RatesRepositoryInterface;
use App\BetterOff\Engine\EmployerCostCalculator;
use App\BetterOff\Engine\Dto\EmployerHireInput;

/**
 * A small set of search-intent landing pages, built from live engine output
 * rather than static copy — today just "cost of hiring an overseas software
 * engineer in the UK", using the one seeded example SOC code (2136).
 * Add further pages by adding another seeded going-rate row plus a route
 * here, not by hand-writing numbers.
 */
class SearchLandingController extends Controller
{
    public function overseasEngineer(RatesRepositoryInterface $rates): Response
    {
        $asOfDate = Carbon::now();
        $calculator = new EmployerCostCalculator($rates);

        $comparison = $calculator->compareUkVsSponsored(
            new EmployerHireInput(
                hireType: EmployerHireInput::HIRE_TYPE_UK,
                annualSalary: 55000,
                employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_SMALL,
                hasSponsorLicence: false,
                asOfDate: $asOfDate,
                socCode: '2136',
            ),
            new EmployerHireInput(
                hireType: EmployerHireInput::HIRE_TYPE_SPONSORED,
                annualSalary: 55000,
                employerSizeClass: EmployerHireInput::EMPLOYER_SIZE_SMALL,
                hasSponsorLicence: false,
                asOfDate: $asOfDate,
                socCode: '2136',
            ),
        );

        return Inertia::render('BetterOff/Search/OverseasEngineer', [
            'uk' => $comparison['uk']->toArray(),
            'sponsored' => $comparison['sponsored']->toArray(),
            'difference' => $comparison['difference'],
            'asOfDate' => $asOfDate->format('Y-m-d'),
        ]);
    }
}
