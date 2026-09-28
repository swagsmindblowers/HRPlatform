# Launch HR / BetterOff.FYI — Architecture Audit

_Workstream 1 output. Written against `main` after merging PR #3 (`claude/ux-revamp-payroll`), which is the actual application — the `main` branch history before that merge contained only a placeholder README._

## Stack

- **Backend:** Laravel 9 (`laravel/framework ^9.0`), PHP 8.0+, PSR-4 autoload under `App\` → `app/`.
- **Frontend:** InertiaJS + Vue (`inertiajs/inertia-laravel`), built with Laravel Mix (`webpack.mix.js`), Yarn.
- **Auth:** Laravel Jetstream + Fortify + Sanctum (`laravel/jetstream`, `laravel/fortify`, `laravel/sanctum`), plus Socialite providers (GitHub, Google, LinkedIn, Microsoft Azure, Slack, Twitter) for SSO.
- **DB:** Laravel migrations under `database/migrations` (MySQL-compatible; `doctrine/dbal` present for column changes). No Prisma/Drizzle — this is a standard Laravel migration set.
- **Tests:** PHPUnit 9 (`phpunit/phpunit ^9.0`), config at `phpunit.xml`; `tests/` directory with PSR-4 `Tests\` autoload-dev. Cypress config (`cypress.json`) present for frontend/e2e tests.
- **Deployment:** Railway (`railway.json`, `railway.worker.json`), Docker (`Dockerfile`, `docker/`), also Heroku-style `Procfile`/`app.json` and `fortrabbit.yml` remnants from the upstream project. CI/preview deploys post commit statuses (`hrplatform-ux-preview - HRPlatform`, `- queue-worker`), both green on the merged commit.
- **Origin:** This is a fork of the open-source HR platform **OfficeLife** (BSD-3-Clause), renamed in `composer.json` to `launchhr/launchhr`, description "Know how your employees feel." Upstream docs/branding (README, `docs/img/officelife.svg`) still reference OfficeLife and should be swapped for Launch HR branding as a follow-up (not in this scope).

## Auth & tenancy

- Multi-tenant anchor is `App\Models\Company\Company` (table `companies`), fields: `name`, `currency`, `slug`, `location`, `has_dummy_data`, `logo_file_id`, `e_coffee_enabled`, `work_from_home_enabled`, `founded_at`, `code_to_join_company`.
- Users: `App\Models\User\*` namespace (not fully enumerated here — out of scope for the cost-panel attach point). Company↔User relationship is standard Jetstream teams-style.
- All domain models are namespaced `App\Models\Company\*` and belong to a `company_id`.

## Role / hiring / compliance models relevant to this scope

| Model | Table | Notes |
| --- | --- | --- |
| `Position` | `positions` | Just `company_id`, `title`. No salary/location fields. |
| `JobOpening` | `job_openings` | `company_id`, `position_id`, `team_id`, `recruiting_stage_template_id`, `reference_number`, `title`, `description`, `active`/`fulfilled` flags. **No salary or location field.** |
| `Candidate`, `CandidateStage*` | `candidates`, ... | ATS pipeline attached to a `JobOpening`. Not inspected field-by-field in this pass — no salary field surfaced in the model list. |
| `Employee`, `EmployeePositionHistory` | `employees`, `employee_position_history` | Position history only tracks `employee_id`, `position_id`, `started_at`, `ended_at` — **no compensation field.** |
| `CompanyComplianceItem` | `company_compliance_items` | **Already exists.** Fields: `company_id`, `title`, `category` (`insurance`/`tax`/`pension`/`immigration`), `jurisdiction`, `status` (`not_started`/`in_progress`/`complete`), `due_date`, `notes`. Belongs to `Company`, but `Company` has no `complianceItems()` relation defined yet (gap — added in this change). |

**Key finding:** there is no salary/compensation field anywhere in the role or offer chain (`Position`, `JobOpening`, `EmployeePositionHistory`). This matches the scope of work's own spec, where salary is always "entered by user" in the cost panel rather than read from a role record — so this isn't a blocker, just confirms the cost panel takes salary as direct input rather than prefilling it.

**No existing calculator/relocation code** was found anywhere in the merged codebase — the public BetterOff calculator is a genuinely new build, not an extraction. No hardcoded rates to inventory from this repo (the scope's Better Off calculator being extracted is a separate, not-yet-attached codebase).

## Module boundary decision

Per the scope of work's own recommended default: **shared package inside this Laravel repo.** New code lives under `app/BetterOff/` (pure engine + rates access, framework-agnostic where practical) and `app/Models/BetterOff/` (Eloquent models), exposed to both the in-app cost panel and the public calculator via `routes/betteroff.php`. No separate service.

## Gaps against the six scope objectives

1. **Shared engine** — building now (Workstream 4).
2. **Employer cost panel** — no existing salary/location field on the role chain; cost panel will take these as direct inputs, consistent with the scope's own spec.
3. **Candidate view** — net new, no existing take-home logic anywhere in the repo.
4. **Compliance handoff** — `CompanyComplianceItem` already exists and is a reasonable target for the handoff, but it's a flat checklist item, not a sponsored-worker record with typed tasks (RTW check, CoS record, reporting deadlines). Building a `SponsoredWorkerTask` model that generates `CompanyComplianceItem` rows keeps the handoff real rather than a pure stub.
5. **Source + as-at date on every figure** — enforced at the rates-repository level (Workstream 3).
6. **Legal/governance docs** — stubbed per-row in `docs/betteroff/legal/` (Workstream 7), explicitly marked non-authoritative.