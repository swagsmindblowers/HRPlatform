# Advice-boundary policy

**Needed before:** any user-facing text is written.
**Status:** partially enforced in code; policy document itself not written.

## What's already enforced in code
- `App\BetterOff\Engine\EmployerCostCalculator` never returns a pass/fail eligibility verdict — a below-going-rate salary produces a `warnings[]` string only (see the six required engine tests in `tests/Unit/BetterOff/EmployerCostCalculatorTest.php`, specifically `test_salary_below_going_rate_raises_indicative_warning_only`).
- The warning copy itself says \"This is a cost indicator only, not an eligibility assessment\" verbatim in `EmployerCostCalculator::calculate()`.
- Every seeded rate in `database/seeders/BetterOffRatesSeeder.php` carries a `source_url`, and every calculated line item carries the `rate_key` it used (`App\BetterOff\Engine\Dto\LineItem`), so the UI can always show where a number came from rather than presenting it as an unsourced fact.

## What this document still needs
- A wording standard: which words are banned from interface copy (\"eligible\", \"qualifies\", \"guaranteed\", \"you will be approved\") and which are required (\"estimate\", \"indicative\", \"as of &lt;date&gt;\").
- A register of interface copy reviewed against this standard, covering the Vue pages under `resources/js/Pages/BetterOff/` as they're built out further and any new copy added later.

## What's needed from a professional
Given the scope of work notes the product owner's own immigration practice, a formal review against the rules on regulated immigration, tax, and financial advice — not something to self-certify.
