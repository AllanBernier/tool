# Spec 012: Admin Dashboard Statistics

## Priority: P0

## Description
Update the existing Dashboard page to display site statistics and recent activity. Regenerate Wayfinder routes and format code with Pint.

## Acceptance Criteria
- [ ] DashboardController passes data: total tools count (published and draft), comparisons count, categories count, tags count, 5 latest tools added, 5 latest generation jobs (with status)
- [ ] Dashboard.vue updated to display: stat cards (tools, comparisons, categories, tags), recent tools list with status, latest generations list with status (loading icon if in progress)
- [ ] Stats numbers are clickable and redirect to corresponding admin pages
- [ ] Pest test: dashboard displays correct statistics
- [ ] Pest test: unauthenticated access redirects to login
- [ ] All tests pass
- [ ] `php artisan wayfinder:generate` executes without error
- [ ] TypeScript actions are generated for all admin controllers
- [ ] `vendor/bin/pint --dirty` executes with no formatting errors
- [ ] All tests still pass after Wayfinder and Pint

## References
- TASKS.md: T-047 to T-049

**Output when complete:** `<promise>DONE</promise>`
