# DPIA for HR data

**Needed before:** the compliance handoff (Workstream 5) goes live with real data.
**Status:** not started — stub only.

## What this needs to cover
- The right-to-work and immigration status data implied by `betteroff_sponsored_workers` / `betteroff_sponsored_worker_tasks` and the existing `company_compliance_items` table (category `immigration`).
- Salary data held in `betteroff_scenarios.inputs` / `employer_result` (employer view) and `candidate_result` (candidate take-home view).
- Risk of re-identification via the candidate share link (`betteroff_scenarios.share_token`) — note the current implementation expires links after 30 days (`App\Services\BetterOff\ShareScenario`) and never includes employer cost lines in the shared payload.

## What's needed from a professional
A completed Data Protection Impact Assessment, signed off by whoever holds the data protection role from the register above, before any real (non-test) sponsored-worker or scenario data is created in production.
"