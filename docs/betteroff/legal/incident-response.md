# Incident response

**Needed before:** the compliance handoff (Workstream 5) goes live.
**Status:** not started — stub only.

## What this needs to cover
- A breach response plan covering the data held by `betteroff_scenarios` (salary, candidate identity via `candidate_id`) and `betteroff_sponsored_workers`/`betteroff_sponsored_worker_tasks` (immigration status implications).
- The 72-hour ICO notification path and who owns triggering it.
- How a breach in the BetterOff.FYI tables would be detected — no anomaly-detection or audit-alerting exists yet beyond the standard `LogAccountAudit` job already used by `SaveScenario`, `ConfirmSponsoredHire`, and `ShareScenario` (see `app/Services/BetterOff/*`), which records who did what but does not alert anyone.

## What's needed from a professional
A breach response plan owner, and a decision on whether the existing audit log is sufficient detection or whether alerting needs to be built (not built in this pass).
