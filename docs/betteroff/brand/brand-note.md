# BetterOff HR — brand note (Workstream 2)

**Status:** brand structure confirmed by the product owner. Trademark search still not performed (see [../legal/trademark.md](../legal/trademark.md)) — this is now more urgent, not less, since the whole platform carries the name.

## Brand structure
**Confirmed decision: Option B.** The whole platform is renamed **BetterOff HR**, not just the public calculator. This supersedes the earlier Option A default (BetterOff.FYI as calculator-only, "powered by Launch HR" platform underneath) that this file originally recorded.

What changed in code as a result:
- Display name (`config/app.php`, `config/officelife.php`, `.env.example`) is now "BetterOff HR".
- The header/footer wordmark and page-title suffix in `resources/js/Shared/Layout.vue` and `resources/js/Shared/Layout/AuthenticationCardLogo.vue`, the marketing landing page (`resources/js/Pages/Landing/Index.vue`), and the invitation email (`resources/views/emails/company/invitation.blade.php`) all now say "BetterOff HR" instead of "LaunchHR".
- No "powered by" attribution line was needed on `resources/js/Pages/BetterOff/PublicCalculator.vue` — it already just says "BetterOff.FYI" as its own heading, which now matches the platform name directly.
- `README.md` rewritten to introduce BetterOff HR rather than the unadapted upstream OfficeLife copy it still had.

**What was deliberately left alone:** the internal `config('officelife.*')` namespace (30+ call sites), translation files under `resources/lang/{fr,ru,nb_NO}/`, and test names. None of that is user-visible, and renaming it is a large, purely-cosmetic refactor with real regression risk for no user-facing benefit — a decision the product owner confirmed when this was raised.

## Domain
`betteroff.fyi` is the confirmed domain. Per the product owner's decision, the code is prepared for it (`.env.example`'s `APP_URL` now defaults to `https://betteroff.fyi`) but it has **not** been attached to the Railway deployment yet — that's a deliberate follow-up step once DNS is ready to point at it, not an oversight.

## About / Security pages
Not built in this pass. The scope of work notes that a `.fyi` domain can read as lightweight to buyers evaluating an HR data tool, so an About page and a Security page are recommended before real HR data is collected under the BetterOff HR brand — tracked here as a follow-up, not fabricated as already-existing pages.

## Logo
`docs/img/officelife.svg` is still the OfficeLife upstream logo, referenced from `README.md`. A real BetterOff HR logo/wordmark asset is needed — not something this coding session can design. Tracked as a follow-up.

## One-line promise
Not finalised. Draft for consideration: "See the true cost of your next UK hire — sourced, dated, and compared side by side with a sponsored overseas hire." This is a suggestion, not a decided tagline.
