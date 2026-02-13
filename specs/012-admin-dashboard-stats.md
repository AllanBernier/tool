# Spec 012: Admin Dashboard Statistics

## Priority: P0

## Description
Update the existing Dashboard page to display site statistics and recent activity. Regenerate Wayfinder routes and format code with Pint.

## Status: COMPLETE

## Acceptance Criteria
- [x] DashboardController passes data: total tools count (published and draft), comparisons count, categories count, tags count, 5 latest tools added, 5 latest generation jobs (with status)
- [x] Dashboard.vue updated to display: stat cards (tools, comparisons, categories, tags), recent tools list with status, latest generations list with status (loading icon if in progress)
- [x] Stats numbers are clickable and redirect to corresponding admin pages
- [x] Pest test: dashboard displays correct statistics
- [x] Pest test: unauthenticated access redirects to login
- [x] All tests pass
- [x] `php artisan wayfinder:generate` executes without error
- [x] TypeScript actions are generated for all admin controllers
- [x] `vendor/bin/pint --dirty` executes with no formatting errors
- [x] All tests still pass after Wayfinder and Pint

## References
- TASKS.md: T-047 to T-049

**Output when complete:** `<promise>DONE</promise>`
