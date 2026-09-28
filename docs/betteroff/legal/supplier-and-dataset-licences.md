# Supplier and dataset licences

**Needed before:** a cost-of-living or relocation-benchmark dataset is chosen.
**Status:** not started — open decision, per the scope of work's own \"decisions needed\" list.

## Current state in code
`database/seeders/BetterOffRatesSeeder.php` seeds `cost_of_living.*` and `relocation.default_benchmark` rows with `source_url` set to `https://example.invalid/todo-choose-cost-of-living-dataset` and a `notes` field flagging them as estimates. These are placeholders so the engine is runnable and testable — **they are not sourced from any real dataset** and must not be used for a real quote until this decision is made.

## What this needs to cover
- Choice of cost-of-living dataset: a public statistics source (e.g. ONS regional data) vs. a licensed commercial dataset (e.g. Numbeo, Mercer).
- Licence terms review before use — several commercial cost-of-living datasets restrict redistribution or require attribution.
- Once chosen, replace the placeholder rows in the seeder with real values, each with its own `source_url` and `verified_by`/`verified_at`, following the same pattern as the UK government-sourced rows already in the seeder.
