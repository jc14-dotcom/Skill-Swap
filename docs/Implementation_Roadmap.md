# SkillSwap Implementation Roadmap

Date: 2026-04-13

## Goal
This roadmap is designed so you can choose what to implement next based on impact, effort, and risk.

## Current Baseline Snapshot
Implemented and stable:
- Core auth, services, bookings, reviews, middleware, policies, and base tests
- Authenticated home and marketplace flow
- CSRF, auth, guest, and rate-limit middleware

Partially implemented or placeholder:
- Notifications UI exists but backend persistence is not implemented
- Messaging schema exists, service/controller are placeholders
- Admin controller is placeholder
- API and websocket are placeholders
- Feature tests are still shallow script orchestration

## How To Choose
Use this order when choosing tracks:
1. Pick one Primary track from T1 to T4.
2. Add one Quality track from T5 to T7.
3. Leave T8 and T9 for when product behavior is stable.

## Track Menu

### T1. Notifications V1
Priority: High
Impact: High
Effort: Medium (4 to 6 days)
Risk: Medium

Scope:
- Add notifications table migration
- Implement create, list, unread count, and mark-as-read operations
- Trigger notifications on booking created, accepted, rejected, completed, and review submitted
- Render real notification list in navbar dropdown

Start points in code:
- app/Models/Notification.php
- app/Services/NotificationService.php
- app/Views/layouts/navbar.php

Definition of done:
- Unread badge reflects actual unread count
- Clicking notification opens relevant entity page
- Mark-all-read action updates badge immediately
- Tests cover create, read, mark-read behavior

### T2. Messaging V1 (Booking-Scoped)
Priority: High
Impact: High
Effort: Medium to Large (6 to 9 days)
Risk: Medium

Scope:
- Build booking-scoped conversations only (no global chat)
- Provider and seeker can send messages only if they belong to that booking
- Add unread state and last-message preview
- Add simple polling refresh first (real-time later)

Start points in code:
- database/migrations/005_create_messages.sql
- app/Controllers/MessageController.php
- app/Services/MessageService.php

Definition of done:
- Conversation list visible for active participants only
- Unauthorized users cannot access thread endpoints
- Send and receive cycle works from both roles
- Tests cover booking ownership access control

### T3. Password Recovery V1
Priority: Medium
Impact: High
Effort: Medium (3 to 5 days)
Risk: Low to Medium

Scope:
- Add forgot-password request flow
- Add reset token issue, expiry, and one-time use
- Add reset form and password update flow
- Add rate limit on reset request endpoint

Start points in code:
- app/Views/auth/forgot-password.php
- app/Controllers/AuthController.php
- app/Services/AuthService.php

Definition of done:
- Token expires correctly and cannot be reused
- Invalid or expired token returns safe error
- Password update invalidates old login session
- Tests cover reset request and reset consume flows

### T4. Admin Operations V1
Priority: Medium
Impact: High
Effort: Large (8 to 12 days)
Risk: Medium

Scope:
- Add admin-only route group and middleware guard
- Build service moderation queue and review moderation queue
- Add user account flags (active, suspended)
- Add booking dispute action logging

Start points in code:
- app/Controllers/AdminController.php
- app/Middleware/AuthMiddleware.php
- routes/web.php

Definition of done:
- Non-admin cannot access admin endpoints
- Admin can suspend/reactivate users
- Admin moderation actions are logged with actor and timestamp
- Tests cover role authorization and critical actions

### T5. Real Feature Test Layer
Status: Applied (2026-04-13)
Priority: High
Impact: High
Effort: Medium (4 to 6 days)
Risk: Low

Scope:
- Replace script-only feature checks with request-level tests
- Cover auth smoke, booking transitions, review gate, and CSRF rejection
- Add fixtures for seeker, provider, admin, service, booking

Start points in code:
- tests/Feature/FlowRulesTest.php
- tests/run.php

Definition of done:
- Failing behavior is caught by tests before manual QA
- CI-style single command runs all tests reliably
- Feature tests include success and denied paths

### T6. Domain and Data Constraints Hardening
Status: Applied (2026-04-13)
Priority: High
Impact: Medium to High
Effort: Medium (3 to 5 days)
Risk: Low to Medium

Scope:
- Add booking status and service status database constraints
- Add indexes for heavy read paths if missing
- Add booking status history table for auditability

Start points in code:
- database/migrations/002_create_services.sql
- database/migrations/003_create_bookings.sql
- database/migrations/004_create_reviews.sql

Definition of done:
- Invalid states are rejected at DB and service policy levels
- History entries are created on booking status change
- Migration rollback paths are clean

### T7. Logging and Observability Cleanup
Status: Applied (2026-04-13)
Priority: Medium
Impact: Medium
Effort: Small to Medium (2 to 4 days)
Risk: Low

Scope:
- Centralize logging behavior behind one logger service
- Respect logging config levels and output path
- Add request correlation id in error logs

Start points in code:
- core/App.php
- config/logging.php

Definition of done:
- Runtime and exception logs follow one format
- Log level can be changed from environment settings
- Critical actions include actor id and path context

### T8. API Foundation V1
Priority: Medium
Impact: Medium
Effort: Medium (4 to 6 days)
Risk: Medium

Scope:
- Add /api/v1 namespace
- Start with notifications and messages read endpoints
- Add token-based auth for API calls

Start points in code:
- routes/api.php
- core/Router.php

Definition of done:
- Versioned API routes are isolated from web routes
- API auth and error format are consistent
- Basic contract tests pass for exposed endpoints

### T9. Real-Time Upgrade (WebSocket)
Priority: Low for now
Impact: Medium to High
Effort: Large (8 to 12 days)
Risk: High

Scope:
- Real-time message delivery
- Real-time notification badge updates
- Connection auth and presence handling

Start points in code:
- websocket/server.php
- websocket/config.php

Definition of done:
- Reconnect logic is stable
- Offline fallback to polling still works
- Load tests show acceptable behavior under concurrent clients

## Recommended Bundles (Pick One)

### Bundle A. Stability First (Recommended)
Duration: 2 to 3 weeks
Includes: T5, T6, T7, then T3
Outcome: safer releases, faster debugging, fewer regressions

### Bundle B. User Engagement First
Duration: 2 to 3 weeks
Includes: T1, T2, then T5
Outcome: meaningful in-app collaboration and retention features

### Bundle C. Platform and Scale First
Duration: 3 to 4 weeks
Includes: T5, T8, then T9
Outcome: cleaner extension path for mobile clients and real-time features

### Bundle D. Operations First
Duration: 2 to 3 weeks
Includes: T4, T7, then T5
Outcome: better moderation and control before growth

## Suggested First Sprint Plan (If You Pick Bundle A)
Week 1:
- Build request-level feature tests for auth and bookings
- Add booking and service state constraints

Week 2:
- Implement centralized logger and request correlation id
- Add password reset request and consume flow

Week 3:
- Buffer week for fixes, hardening, and final test coverage

## Decision Prompt
Choose one bundle first: A, B, C, or D.
Then choose one optional add-on track from the remaining list.
