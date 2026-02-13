# Spec 037: Final Tests & Formatting

## Priority: P2

## Description
Run the complete test suite, fix any failures, and format the entire codebase with Pint, Prettier, and ESLint.

## Acceptance Criteria
- [ ] `php artisan test` passes with 0 failures and 0 errors
- [ ] Coverage of all public and admin pages
- [ ] Coverage of all generation jobs
- [ ] Coverage of all form validations
- [ ] `vendor/bin/pint` produces no changes (everything already formatted)
- [ ] `npm run format` produces no changes
- [ ] `npm run lint` produces no errors
- [ ] Code is clean and consistent throughout

## References
- TASKS.md: T-124 to T-125

**Output when complete:** `<promise>DONE</promise>`
