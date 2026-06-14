---
name: php-test-debug
description: Run PHPUnit tests, diagnose failures, fix, and re-run until all pass — with guardrails against getting stuck.
---

# PHP Test Debugging Workflow

Run PHPUnit tests, diagnose any failures, fix them, and re-run until all pass. Includes guardrails to avoid the common "stuck in a test loop" pattern.

## When to use

- User says "run the tests" or "make sure tests pass"
- After implementing changes that might break tests
- When tests are failing and need diagnosis

## Procedure

### 1. Run tests first (baseline)

```bash
php vendor/bin/phpunit --testsuite Unit 2>&1
```

Capture the output. If all pass, you're done. If not, proceed.

### 2. Categorize failures

Before editing anything, classify each failure:

| Category | Example | Fix approach |
|----------|---------|--------------|
| **Wrong assertion** | Test expects old behavior you changed | Update test to match new behavior |
| **Missing setup** | Test doesn't set up required DB state | Add setUp() data or fixture |
| **Environment issue** | SQLite vs MySQL differences | Use DB-agnostic assertions |
| **PHP warning** | `array_key_exists` on object | Change to `isset()` or suppress with `@` |
| **Side effect** | Previous test left stale state | Add cleanup in setUp()/tearDown() |

### 3. Fix one category at a time

Do NOT fix all failures at once. Pick one category, fix all instances, re-run. Repeat.

### 4. Guardrails against getting stuck

**If you fix a test and it still fails:**
1. Re-read the actual test output — don't assume what went wrong
2. Check if the failure is in a *different* test than the one you edited
3. Run just that one test: `php vendor/bin/phpunit --filter=testMethodName 2>&1`
4. If the test passes in isolation but fails in suite, it's a side-effect issue — check setUp/tearDown

**If you've edited the same test file 3+ times:**
1. Stop and re-read the entire test file from scratch
2. Re-read the source code being tested
3. Write a minimal reproduction script outside PHPUnit to verify behavior:
   ```php
   php -r "require 'vendor/autoload.php'; print_r(YourClass::method());"
   ```
4. Only then update the test

**If tests pass but you're not sure they're correct:**
1. Check that assertions actually exercise the code path — not just "no exception thrown"
2. Verify edge cases: empty input, null, missing keys
3. Run with `--verbose` to see test names and order

### 5. Final verification

After all fixes:
1. Run full suite: `php vendor/bin/phpunit --testsuite Unit 2>&1`
2. Also run integration tests if they exist: `php tests/run.php 2>&1`
3. Confirm clean output — no warnings, no deprecation notices

## Common pitfalls in this project

- **PHPUnit `failOnWarning`**: PHP notices about undefined array keys on nullable fields trigger warnings. Use `isset()` instead of `array_key_exists()` in assertions, or set `failOnWarning="false"` in phpunit.xml.
- **Password reset token tests**: Tokens are logged (not in session), so tests must read from `storage/logs/app.log`. Clear the log before test runs to avoid stale tokens.
- **Database migration conflicts**: New indexes may conflict with existing ones. Use `ALTER TABLE ... ADD INDEX` instead of `CREATE INDEX` for idempotent migrations.
- **Custom test runner vs PHPUnit**: This project has both `tests/run.php` (integration) and PHPUnit (unit). Run both to be thorough.
