# AI governance

**Needed before:** any AI feature ships.
**Status:** not applicable yet — no AI feature exists in the BetterOff.FYI module as built in this pass. The calculation engine (`App\BetterOff\Engine\*`) is entirely deterministic, rule-based PHP — no model inference, no LLM calls, nothing that makes an automated decision about an individual.

## What this needs to cover if an AI feature is added later
- Human review requirement for any AI-assisted output shown to a candidate or employer.
- An explicit statement that no automated decision about an individual (e.g. \"should we hire this person\", \"is this person eligible\") is ever made by an AI feature — consistent with the advice-boundary policy above.
- Note: this repository already has an unrelated AI chat feature elsewhere in the app (see `RateLimiter::for('ai-chat', ...)` in `app/Providers/RouteServiceProvider.php`) — if this document is written, check whether it needs to cover that feature too, since it's out of scope for BetterOff.FYI specifically.
