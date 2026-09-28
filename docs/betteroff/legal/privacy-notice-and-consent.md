# Privacy notice and consent

**Needed before:** public calculator launch.
**Status:** not started — stub only.

## What this needs to cover
- A privacy notice for the public calculator (`app/Http/Controllers/BetterOff/PublicCalculatorController.php`), which by design collects no employee-level personal data — only role-level inputs (salary, location, hire type). The notice should say so explicitly, since it simplifies the privacy position considerably versus the in-app product.
- A separate notice section for the in-app cost panel and sponsored-worker records, which do hold real candidate/employee data.
- Cookie/analytics consent set-up for whatever privacy-friendly analytics tool is chosen for Workstream 6's measurement step (no analytics integration exists in this codebase yet — not built in this pass).

## What's needed from a professional
Legal review of the notice text; a product decision on the analytics tool (see [supplier-and-dataset-licences.md](./supplier-and-dataset-licences.md)).
"