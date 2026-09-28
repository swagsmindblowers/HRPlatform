# Retention schedule

**Needed before:** the compliance handoff (Workstream 5) goes live.
**Status:** not started — stub only.

## What this needs to cover
- Retention period for `betteroff_scenarios` rows (currently retained indefinitely — no automatic deletion is implemented).
- Retention period for `betteroff_sponsored_workers` / `betteroff_sponsored_worker_tasks`, checked against Home Office sponsor record-keeping requirements (sponsors must keep certain records for the duration of sponsorship plus a defined period after) and UK employment record-keeping norms.
- Whether `betteroff_rate_versions` history should ever be purged (recommendation: no — historical rates must stay queryable so old scenarios remain reproducible; see Workstream 4's \"as-at date\" requirement).

## What's needed from a professional
Confirmed retention periods from someone with current Home Office sponsor guidance and UK employment-record-keeping knowledge; a follow-up engineering task to implement the resulting deletion/archival job once periods are confirmed (not built in this pass — no code currently enforces a retention limit).
"