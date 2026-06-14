# SkillSwap Full Tailwind Migration Plan

Date: 2026-04-20
Status: Ready for implementation
Objective: Complete a full Tailwind-only UI migration while preserving current visual design, removing CDN runtime dependencies, and preventing broken image icons.

## 1) Goals And Constraints

1. Preserve the current look and feel (guest oval navbar, maroon palette, existing spacing and hierarchy).
2. Eliminate legacy `ss-*` class usage in view templates and JavaScript behavior hooks.
3. Keep runtime fully offline-capable (no CDN dependency for fonts or styles).
4. Ensure profile and non-profile image rendering always falls back to a valid local default image.
5. Keep migration incremental and releasable by phase.

## 2) Current Audit Snapshot

### Legacy styling footprint

- Legacy view references:
  - 451 `ss-*` references across 16 files.
- Legacy stylesheet:
  - `resources/css/input.css` has 2726 lines.
  - 472 `.ss-*` selectors remain.
- JavaScript coupling:
  - 15 `ss-*` references in `public/assets/js/app.js`.

### Highest-impact remaining files (by `ss-*` count)

1. `app/Views/services/index.php` (126)
2. `app/Views/profile/settings.php` (44)
3. `app/Views/services/edit.php` (33)
4. `app/Views/home/index.php` (28)
5. `app/Views/messages/chat.php` (27)
6. `app/Views/services/create.php` (25)
7. `app/Views/bookings/create.php` (24)
8. `app/Views/reviews/create.php` (24)
9. `app/Views/dashboard/index.php` (22)
10. `app/Views/services/my-services.php` (17)
11. `app/Views/bookings/index.php` (17)
12. `app/Views/bookings/provider-bookings.php` (16)
13. `app/Views/messages/index.php` (16)
14. `app/Views/bookings/my-bookings.php` (15)
15. `app/Views/errors/500.php` (9)
16. `app/Views/errors/404.php` (8)

### External runtime dependency

Google Fonts CDN links still exist in:

- `app/Views/layouts/app.php`
- `app/Views/layouts/guest.php`
- `app/Views/errors/404.php`
- `app/Views/errors/500.php`

### Image fallback baseline

- Profile helper exists: `profile_image_url()` in `app/Helpers/format.php`.
- Most profile images already call this helper.
- Global fallback handling for all `img` tags is not yet standardized.

## 3) Migration Strategy

Use a phased, low-risk migration with acceptance gates after each phase.

1. Foundations first (fonts offline, image fallback contract, JS decoupling pattern).
2. Convert highest-risk/highest-volume templates first.
3. Decommission legacy CSS only after all references are removed and verified.

## 4) Phase Plan

## Phase A - Foundations And Guardrails

### Scope

- Finalize reusable UI primitives in Tailwind (buttons, cards, form controls, badges, table wrappers).
- Establish migration guardrails for JS hooks and image fallback.

### Files

- `tailwind.config.js`
- `resources/css/input.css`
- `public/assets/js/app.js`
- `app/Helpers/format.php`

### Tasks

1. Add a small component utility layer in Tailwind (`@layer components`) for repeated patterns to keep templates readable.
2. Replace JS dependency on style classes with data attributes where behavior is intended:
   - `.ss-field` -> `[data-field]`
   - `.ss-field-error` -> generated node with stable data marker
   - `.ss-file-input-hidden` -> `[data-file-input-hidden]`
   - `.ss-password-field` / `.ss-password-toggle` -> `[data-password-field]` / `[data-password-toggle]`
3. Add image fallback initializer in JS:
   - Target all `img[data-fallback-src]`.
   - On error, switch once to local fallback and prevent infinite loops.
4. Add a generic image helper in PHP for non-profile contexts, while keeping `profile_image_url()` for avatar-specific semantics.

### Acceptance Gate

- Existing flows (drawer, popovers, profile photo controls, live search, form validation) still work.
- No visual regressions on already migrated pages.

## Phase B - Offline Font Pipeline (No CDN)

### Scope

Remove runtime font CDN links and self-host required fonts.

### Files

- `app/Views/layouts/app.php`
- `app/Views/layouts/guest.php`
- `app/Views/errors/404.php`
- `app/Views/errors/500.php`
- `resources/css/input.css`
- `public/assets/fonts/*` (new)

### Tasks

1. Add local font files for Manrope and Sora under `public/assets/fonts/`.
2. Define `@font-face` in CSS (or in a dedicated imported file) with local URLs.
3. Remove all `fonts.googleapis.com` and `fonts.gstatic.com` links from layouts and error views.
4. Verify Tailwind font families continue mapping to `font-sans` and `font-heading`.

### Acceptance Gate

- No external font requests in browser network tab.
- Typography remains visually consistent.

## Phase C - Page Cluster Migration To Tailwind

### Cluster C1 - Marketing Landing (largest and highest risk)

#### File

- `app/Views/services/index.php`

#### Tasks

1. Convert all remaining `ss-*` sections to Tailwind markup:
   - marketplace board
   - about
   - newsletter
   - portfolio
   - team
   - workflow
   - testimonials
   - stories/blog
2. Preserve existing visual style and maroon identity.
3. Replace static inline style fragments with utilities/tokens.
4. Add `data-fallback-src` to all images.

#### Gate

- Page has zero `ss-*` classes.
- Hero and CTA interactions remain consistent with current behavior.

### Cluster C2 - Forms and account area

#### Files

- `app/Views/profile/settings.php`
- `app/Views/services/create.php`
- `app/Views/services/edit.php`
- `app/Views/bookings/create.php`
- `app/Views/reviews/create.php`

#### Tasks

1. Replace legacy form shell/field classes with Tailwind primitives.
2. Add data attributes required by JS validation/password toggles.
3. Keep accessibility attributes and labels intact.
4. Ensure profile photo preview/remove flow remains intact and uses fallback attributes.

#### Gate

- Client-side validation and password toggle still work.
- No broken layout on desktop/mobile.

### Cluster C3 - Dashboard, bookings, messages lists/tables

#### Files

- `app/Views/home/index.php`
- `app/Views/dashboard/index.php`
- `app/Views/services/my-services.php`
- `app/Views/bookings/index.php`
- `app/Views/bookings/provider-bookings.php`
- `app/Views/bookings/my-bookings.php`
- `app/Views/messages/index.php`
- `app/Views/messages/chat.php`

#### Tasks

1. Convert cards/tables/chips/actions from `ss-*` to Tailwind.
2. Preserve role-specific states, status chips, and action logic.
3. Keep clickable card behavior and keyboard accessibility.

#### Gate

- All list/table views have zero `ss-*` classes.
- Actions and status transitions remain functional.

### Cluster C4 - Error views

#### Files

- `app/Views/errors/404.php`
- `app/Views/errors/500.php`

#### Tasks

1. Rebuild with Tailwind-only layout.
2. Reuse local font setup from Phase B.
3. Preserve safe error message rendering and navigation CTA.

#### Gate

- Error pages render correctly with no legacy class dependency.

## Phase D - Image Fallback Hardening

### Scope

Guarantee graceful image rendering for profile and non-profile contexts.

### Files

- `app/Helpers/format.php`
- `public/assets/js/app.js`
- all templates with `<img>` tags
- `public/assets/images/default-avatar.png` (existing)
- `public/assets/images/default-image.svg` (new recommended)

### Tasks

1. Standardize helper usage in templates:
   - profile images use `profile_image_url()`
   - non-profile images use generic helper.
2. Add `data-fallback-src` for every `img` and initialize fallback handling in JS.
3. Ensure dynamic cards rendered by live search also set fallback data.
4. Keep fallback assets local and always present.

### Gate

- Removing/renaming source images should not produce broken image icon in UI.

## Phase E - Legacy CSS Decommission

### Scope

Remove `ss-*` bridge safely after templates and JS are fully migrated.

### Files

- `resources/css/input.css`
- `public/assets/css/app.css` (build output)

### Tasks

1. Confirm no `class="ss-` remains in `app/Views/**/*.php`.
2. Confirm no `ss-` behavior hooks remain in `public/assets/js/app.js`.
3. Delete obsolete `.ss-*` blocks from `resources/css/input.css`.
4. Keep only:
   - Tailwind directives
   - token definitions still needed
   - minimal custom component layer (if intentionally retained)
5. Run production build and verify generated CSS size decreases.

### Gate

- Zero runtime dependency on legacy `ss-*` selectors.
- App remains visually consistent with current approved design.

## 5) Verification Checklist Per Phase

1. Build CSS:
   - `npm run build`
2. Run PHP test suite:
   - `php tests/run.php`
3. Manual smoke checks:
   - Guest: landing, auth pages, marketplace search.
   - Member: home, dashboard, bookings, messages, profile settings.
   - Error pages: 404 and 500 rendering.
4. Responsive checks:
   - 360px mobile, 768px tablet, 1280px desktop.
5. Accessibility spot checks:
   - keyboard navigation for cards/buttons/forms.
   - visible focus states.

## 6) Risk Register And Mitigation

1. Risk: JS validation breaks after class removal.
   - Mitigation: migrate behavior hooks to data attributes before removing old classes.
2. Risk: visual drift on landing page after large conversion.
   - Mitigation: section-by-section conversion with screenshot comparison checkpoints.
3. Risk: fallback loops for broken images.
   - Mitigation: one-time `onerror` guard in JS.
4. Risk: font mismatch after CDN removal.
   - Mitigation: include local weights currently used (Manrope 400/500/700/800, Sora 600/700/800).

## 7) Definition Of Done

1. No `ss-*` classes in view templates.
2. No `.ss-*` selectors in source stylesheet.
3. No CDN font or style dependency in runtime templates.
4. All images have a local fallback path and no broken image icon appears.
5. Build and tests pass; core user flows are verified on mobile and desktop.

## 8) Recommended Execution Order

1. Phase A (foundations)
2. Phase B (offline fonts)
3. Phase C1 (landing)
4. Phase C2 (forms + profile)
5. Phase C3 (dashboard/bookings/messages)
6. Phase C4 (error pages)
7. Phase D (global image fallback verification)
8. Phase E (legacy CSS decommission)

This order minimizes regressions while preserving current design at each checkpoint.
