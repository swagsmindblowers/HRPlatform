# Data protection roles and register

**Needed before:** any customer data is collected.
**Status:** not started — stub only.

## What this needs to cover
- Who is the data controller and who (if anyone) is a processor, for both BetterOff HR generally and the BetterOff.FYI module specifically (the public calculator is designed to collect no employee-level personal data — see `app/Http/Controllers/BetterOff/PublicCalculatorController.php` — but the in-app cost panel and sponsored-worker records do hold real candidate/employee data via `betteroff_scenarios`, `betteroff_sponsored_workers`).
- ICO registration check and fee payment if the controller test applies.
- A record of processing activities (ROPA) entry for: scenario inputs/results (`betteroff_scenarios`), sponsored-worker compliance tasks (`betteroff_sponsored_worker_tasks`), and the existing `company_compliance_items` table they feed into.

## What's needed from a professional
A data protection lead or external DPO/lawyer to confirm the controller/processor split and complete the ICO registration if required.
