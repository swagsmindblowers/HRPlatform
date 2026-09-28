<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BetterOff\RateVersion;

/**
 * Seed data for the BetterOff.FYI rates table.
 *
 * IMPORTANT: the values below are plausible ballpark UK figures used to make
 * the engine runnable and testable. They are NOT verified against live
 * gov.uk/HMRC pages as part of this change — per Workstream 8 ("Figure
 * verification"), every row must be opened at its official source and
 * hand-checked, with verified_by/verified_at filled in, before this seeder's
 * output is used for a real quote. Cost-of-living and relocation rows are
 * explicitly-labelled estimates pending the open dataset decision in the
 * scope of work ("Cost-of-living and relocation dataset").
 */
class BetterOffRatesSeeder extends Seeder
{
    public function run()
    {
        $rows = [
            ['key' => 'uk.income_tax.personal_allowance', 'value' => 12570, 'unit' => 'gbp', 'source_url' => 'https://www.gov.uk/income-tax-rates'],
            ['key' => 'uk.income_tax.basic_rate', 'value' => 0.20, 'unit' => 'rate', 'source_url' => 'https://www.gov.uk/income-tax-rates'],
            ['key' => 'uk.employee_ni.rate', 'value' => 0.08, 'unit' => 'rate', 'source_url' => 'https://www.gov.uk/national-insurance-rates-letters'],
            ['key' => 'uk.employee_ni.threshold', 'value' => 12570, 'unit' => 'gbp', 'source_url' => 'https://www.gov.uk/national-insurance-rates-letters'],
            ['key' => 'uk.employer_ni.rate', 'value' => 0.138, 'unit' => 'rate', 'source_url' => 'https://www.gov.uk/national-insurance-rates-letters'],
            ['key' => 'uk.employer_ni.threshold', 'value' => 9100, 'unit' => 'gbp', 'source_url' => 'https://www.gov.uk/national-insurance-rates-letters'],
            ['key' => 'uk.pension.employer_min_rate', 'value' => 0.03, 'unit' => 'rate', 'source_url' => 'https://www.thepensionsregulator.gov.uk/en/employers/new-employers/im-an-employer-what-are-my-duties/contributions'],
            ['key' => 'uk.visa.skilled_worker.application_fee', 'value' => 719, 'unit' => 'gbp', 'source_url' => 'https://www.gov.uk/skilled-worker-visa/fees'],
            ['key' => 'uk.visa.skilled_worker.ihs_per_year', 'value' => 1035, 'unit' => 'gbp', 'source_url' => 'https://www.gov.uk/healthcare-immigration-application'],
            ['key' => 'uk.sponsorship.cos_fee', 'value' => 239, 'unit' => 'gbp', 'source_url' => 'https://www.gov.uk/guidance/immigration-rules/immigration-rules-appendix-skilled-worker'],
            ['key' => 'uk.sponsorship.licence_fee.small_employer', 'value' => 536, 'unit' => 'gbp', 'source_url' => 'https://www.gov.uk/uk-visa-sponsorship-employers/apply-for-a-licence'],
            ['key' => 'uk.sponsorship.licence_fee.large_employer', 'value' => 1476, 'unit' => 'gbp', 'source_url' => 'https://www.gov.uk/uk-visa-sponsorship-employers/apply-for-a-licence'],

            // Skilled Worker going rate — one example SOC code seeded as a placeholder.
            // A real SOC-code table (hundreds of rows) is out of scope for this pass;
            // this exists so the "below going rate" warning path is testable end to end.
            ['key' => 'uk.skilled_worker.going_rate.2136', 'value' => 34000, 'unit' => 'gbp', 'source_url' => 'https://www.gov.uk/guidance/immigration-rules/immigration-rules-appendix-skilled-occupations', 'notes' => 'SOC 2136 (Programmers and software development professionals) — placeholder, verify against current appendix.'],

            // Cost-of-living / relocation — explicitly estimates, dataset choice is an open decision in the scope of work.
            ['key' => 'cost_of_living.uk.london', 'value' => 2200, 'unit' => 'gbp_per_month', 'source_url' => 'https://example.invalid/todo-choose-cost-of-living-dataset', 'notes' => 'ESTIMATE — placeholder pending dataset/licence decision.'],
            ['key' => 'cost_of_living.uk.manchester', 'value' => 1500, 'unit' => 'gbp_per_month', 'source_url' => 'https://example.invalid/todo-choose-cost-of-living-dataset', 'notes' => 'ESTIMATE — placeholder pending dataset/licence decision.'],
            ['key' => 'cost_of_living.overseas.default', 'value' => 1200, 'unit' => 'gbp_per_month', 'source_url' => 'https://example.invalid/todo-choose-cost-of-living-dataset', 'notes' => 'ESTIMATE — placeholder pending dataset/licence decision.'],
            ['key' => 'relocation.default_benchmark', 'value' => 3000, 'unit' => 'gbp', 'source_url' => 'https://example.invalid/todo-choose-relocation-benchmark-dataset', 'notes' => 'ESTIMATE — placeholder pending dataset decision.'],
        ];

        foreach ($rows as $row) {
            RateVersion::updateOrCreate(
                ['key' => $row['key'], 'effective_from' => '2024-04-06'],
                array_merge(['effective_from' => '2024-04-06', 'effective_to' => null], $row)
            );
        }

        // A historical Immigration Skills Charge rate for the small-employer band,
        // to exercise the "as-at date before a rate change returns the earlier rate" case.
        RateVersion::updateOrCreate(
            ['key' => 'uk.sponsorship.skills_charge.small_employer_per_year', 'effective_from' => '2020-01-01'],
            [
                'value' => 364,
                'unit' => 'gbp_per_year',
                'effective_from' => '2020-01-01',
                'effective_to' => '2023-03-31',
                'source_url' => 'https://www.gov.uk/guidance/immigration-skills-charge-employer-guidance',
                'notes' => 'Historical rate, superseded 2023-04-01.',
            ]
        );
        RateVersion::updateOrCreate(
            ['key' => 'uk.sponsorship.skills_charge.small_employer_per_year', 'effective_from' => '2023-04-01'],
            [
                'value' => 364,
                'unit' => 'gbp_per_year',
                'effective_from' => '2023-04-01',
                'effective_to' => null,
                'source_url' => 'https://www.gov.uk/guidance/immigration-skills-charge-employer-guidance',
            ]
        );
        RateVersion::updateOrCreate(
            ['key' => 'uk.sponsorship.skills_charge.large_employer_per_year', 'effective_from' => '2024-04-06'],
            [
                'value' => 1000,
                'unit' => 'gbp_per_year',
                'effective_from' => '2024-04-06',
                'effective_to' => null,
                'source_url' => 'https://www.gov.uk/guidance/immigration-skills-charge-employer-guidance',
            ]
        );
    }
}
