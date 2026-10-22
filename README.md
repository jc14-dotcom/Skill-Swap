# SkillSwap Architecture v2

This repository now contains an implemented revised architecture baseline for SkillSwap.

Implemented modules:

- Core runtime (App, Router, Request, Response, Session, Validator, Database, Model)
- Middleware layer (auth, guest, csrf, throttling)
- Policy layer (service, booking transition, review)
- MVP domains (auth, service listing/creation, booking lifecycle, reviews)
- SQL migrations and optional seeders

## Requirements

- PHP 8.2+
- MySQL 8+ (or compatible)

## Setup

1. Update environment values in .env for your local database.
2. Run SQL migration files in order from database/migrations.
3. Optionally run seeders by using database/seed.php after migrations. Seeders are restricted to local/testing environments and require `SEED_DEMO_PASSWORD` in `.env`.
4. Start app from web server root pointing to /public.

## Local Run (PHP built-in server)

Use Composer script:

- composer run serve

Or direct command:

- php -S localhost:8000 -t public

## Notes

- CSRF is enabled for state-changing routes.
- Booking transitions are enforced by policy/state machine rules.
- Reviews are allowed only for completed bookings by participants.
- Messaging/admin/API are still reserved for future phases.
