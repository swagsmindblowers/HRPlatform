# Golden scenarios (Workstream 8)

Three scenarios run through the live engine (seeded rates, via `php artisan tinker`) and hand-checked against the arithmetic below. These exercise the seeded placeholder rates in `database/seeders/BetterOffRatesSeeder.php` — **re-run and re-check this file once the real gov.uk/HMRC figures are verified** (see [legal/README.md](./legal/README.md) and the seeder's own header comment). This is not a substitute for the accountant review the scope of work calls for.

## Scenario A — UK vs. sponsored hire, £60,000 salary, 5-year contract, licence already held

As of 2024-06-01, small employer, SOC 2136.

- **UK hire total: £68,824.20** = salary 60,000 + employer NI 7,024.20 (= (60,000−9,100) × 0.138) + employer pension 1,800.00 (= 60,000 × 0.03). Every sponsorship line is £0.
- **Sponsored hire total: £76,777.20** = the same 68,824.20 + certificate of sponsorship fee 239.00 + Immigration Skills Charge 1,820.00 (= 364 × 5 years, small-employer rate) + visa fees & health surcharge 5,894.00 (= 719 + 1,035 × 5, employer pays 100%) + sponsor licence fee £0 (company already holds a licence).
- **Difference: £7,953.00.**

## Scenario B — Candidate relocating Manchester → London

Current salary £45,000 in Manchester, new salary £65,000 in London, as of 2024-06-01.

- **Better off by: £500.00 / month**, after basic-rate tax/NI and the seeded (placeholder, estimate) cost-of-living figures for each city.
- Assumptions listed alongside the figure: basic-rate-band-only tax/NI, and that the housing/living cost numbers are indicative estimates, not a guarantee — both surfaced automatically by `CandidateTakeHomeCalculator`.

## Scenario C — Sponsored hire below the going rate, no licence, large employer

Salary £30,000, SOC 2136 (seeded going rate £34,000), 2-year contract, as of 2024-06-01.

- **Total: £40,288.20** = salary 30,000 + employer NI 2,884.20 + employer pension 900.00 + CoS fee 239.00 + Immigration Skills Charge 2,000.00 (= 1,000 × 2 years, large-employer rate) + visa fees 2,789.00 (= 719 + 1,035 × 2) + sponsor licence fee 1,476.00 (large-employer rate, no licence held).
- **Two warnings raised, matching the required test cases:** the salary-below-going-rate warning (explicitly \"not an eligibility assessment\") and the no-licence lead-time warning. Neither warning blocks the calculation or changes it into a pass/fail result — the total is still computed normally.

## How these were produced

```
php artisan tinker --execute="... EmployerCostCalculator::compareUkVsSponsored(...) / CandidateTakeHomeCalculator::calculate(...) ..."
```

against the sqlite database seeded by `BetterOffRatesSeeder`, in this session. Re-run the same calls after any change to the seeder or the engine to re-verify these numbers still match.
"