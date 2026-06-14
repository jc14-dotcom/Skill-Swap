# SkillSwap System - Revised Plan v2

Date: 2026-04-10
Status: Architecture only (no implementation in this document)

## 1) Purpose

This v2 plan keeps the current scaffold and improves long-term maintainability, security, and extensibility.

Scope labels used in this plan:

- [CORE] Build in MVP
- [OPTIONAL] Build after MVP if needed
- [FUTURE] Reserved for later phases

## 2) What Changed from v1

1. Security baseline moved to CORE: CSRF, authorization checks, input validation, secure session settings.
2. Minimal tests moved to CORE: smoke and booking flow tests.
3. Explicit ownership added between core classes and helper functions.
4. Middleware and policy layers introduced for cleaner access control.
5. Booking status transitions formalized as a state machine.
6. API and async growth path documented without forcing early implementation.

## 3) Architecture Style

Use a modular monolith with clear boundaries:

- Controllers: HTTP orchestration only.
- Services: business use cases.
- Models: data access and persistence concerns.
- Core: framework/runtime primitives.
- Helpers: small pure utility helpers only (no stateful business logic).

Guideline: keep controllers thin and move business rules to Services.

## 4) Revised Folder Plan (Target)

The existing scaffold is valid. Add the following as architecture upgrades when you start implementation.

```text
/skillswap/
  app/
    Controllers/                [CORE]
    Models/                     [CORE]
    Services/                   [CORE]
    Middleware/                 [CORE]   auth, guest, csrf, role checks
    Policies/                   [CORE]   booking/service ownership rules
    DTO/                        [OPTIONAL] request/response payload shaping
    Events/                     [FUTURE] domain events
    Listeners/                  [FUTURE] notifications, async handlers
    Exceptions/                 [OPTIONAL] domain and validation exceptions
    Views/                      [CORE]
    Helpers/                    [CORE]   stateless utilities only

  core/
    App.php                     [CORE]
    Router.php                  [CORE]
    Request.php                 [CORE]
    Response.php                [CORE]
    Controller.php              [CORE]
    Model.php                   [CORE]
    Database.php                [CORE]
    Session.php                 [CORE]
    Validator.php               [CORE]

  config/
    app.php                     [CORE]
    database.php                [CORE]
    constants.php               [OPTIONAL]
    logging.php                 [OPTIONAL]

  routes/
    web.php                     [CORE]
    api.php                     [FUTURE]

  tests/
    Feature/                    [CORE]
    Unit/                       [OPTIONAL]

  storage/
    logs/                       [CORE]
    uploads/                    [CORE]
```

## 5) Responsibility Rules (Important)

To avoid duplicated logic:

1. `core/Session.php` owns session lifecycle and storage behavior.
2. `app/Helpers/session.php` may only expose thin wrappers, if needed.
3. `core/Validator.php` owns validation engine behavior.
4. `app/Helpers/validator.php` may only provide rule presets or formatting helpers.
5. `core/Response.php` owns HTTP response shaping.
6. `app/Helpers/response.php` may only provide shortcut formatters.

If a helper starts containing business rules, move that logic to a Service class.

## 6) Security Baseline (CORE)

Required in MVP:

1. CSRF protection on all state-changing endpoints.
2. Server-side validation for all form inputs.
3. Authorization checks for provider-only and owner-only actions.
4. Session hardening: `HttpOnly`, `Secure` in HTTPS, `SameSite=Lax` or stricter.
5. Password hashing with strong algorithm defaults.
6. Rate limiting on login and sensitive actions.

## 7) Booking Domain Rules

### 7.1 Status State Machine

Primary statuses:

- `pending`
- `accepted`
- `completed`
- `cancelled`
- `rejected` (recommended explicit status)

Transitions:

1. `pending -> accepted` by provider only.
2. `pending -> rejected` by provider only.
3. `pending -> cancelled` by seeker only.
4. `accepted -> completed` by provider (or system confirmation rule).
5. `accepted -> cancelled` by seeker/provider under allowed cancellation window.

Terminal states:

- `completed`
- `cancelled`
- `rejected`

### 7.2 Review Rule

Review creation is allowed only when booking is `completed` and only by participants.

## 8) Routing Conventions

Page routes and action routes can stay simple in MVP, but keep HTTP semantics clear:

1. `GET` for reads/pages.
2. `POST` for creates/actions.
3. Plan for `PUT/PATCH/DELETE` compatibility (method override is acceptable in web forms).

Example pattern:

- `GET /services`
- `GET /services/{id}`
- `POST /services`
- `POST /bookings`
- `PATCH /bookings/{id}/status` (or POST with `_method=PATCH`)

## 9) Data and Database Guidelines

CORE database constraints:

1. Foreign keys for all relational links (`bookings.user_id`, `bookings.service_id`, etc).
2. Indexes on frequent filters:
   - `services.user_id`
   - `bookings.user_id`
   - `bookings.provider_id` (if present)
   - `bookings.status`
   - `reviews.service_id`
3. Timestamps on core entities.
4. Soft delete policy optional, but decide early to avoid migration churn.

## 10) Testing Strategy (Revised)

Minimum CORE tests to implement with MVP:

1. Auth flow smoke test (register/login/logout).
2. Service creation authorization test.
3. Booking creation test.
4. Booking status transition authorization test.
5. Review allowed only after completion test.

OPTIONAL test expansion:

1. Validation rule edge cases.
2. Service listing filters/search.
3. Rate limit behavior tests.

## 11) Future-Ready Extension Points

Keep these seams now so future modules fit cleanly:

1. Messaging module linked by `booking_id`.
2. Notification dispatch interface (DB now, async later).
3. API route namespace with versioning (`/api/v1`).
4. Event hooks after important actions:
   - booking created
   - booking accepted/rejected
   - booking completed
   - review submitted

## 12) Delivery Plan

Phase 1 [CORE]:

1. Auth
2. Services
3. Booking
4. Review
5. Security baseline
6. Minimal feature tests

Phase 2 [OPTIONAL]:

1. Dashboard improvements
2. UI component refinements
3. Upload enhancements
4. Additional tests and exception structure

Phase 3 [FUTURE]:

1. Messaging
2. WebSocket/push updates
3. Admin panel
4. Public API
5. Notifications

## 13) Definition of Done for MVP

MVP is considered done only if:

1. Core user flow works end-to-end.
2. Security baseline is active.
3. Booking state transitions are enforced by rule.
4. Critical feature tests pass.
5. Logs are written for major failures.

---

This plan is intentionally implementation-agnostic. It is a structural and policy guide for the next coding phase.