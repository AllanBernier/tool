# Spec 019: Admin Generation Endpoints

## Priority: P0

## Description
Create the GenerationController for dispatching AI generation jobs from the admin, wire up routes, connect buttons in admin Vue pages, write tests, and regenerate Wayfinder.

## Acceptance Criteria
- [ ] Controller at `app/Http/Controllers/Admin/GenerationController.php`
- [ ] Methods: `generateTool(Tool $tool)` dispatches `GenerateToolContent` and redirects with flash, `generateComparison(Comparison $comparison)` dispatches `GenerateComparisonContent` and redirects with flash, `suggestAlternatives(Tool $tool)` dispatches `SuggestAlternativesAndComparisons` and redirects, `fetchLogo(Tool $tool)` dispatches `FetchToolLogo` and redirects
- [ ] Verifies status is not already `generating` before dispatching
- [ ] Routes in `routes/admin.php`: `POST /dashboard/outils/{tool}/generate`, `POST /dashboard/comparatifs/{comparison}/generate`, `POST /dashboard/outils/{tool}/suggest-alternatives`, `POST /dashboard/outils/{tool}/fetch-logo`
- [ ] Routes named `admin.generate.*`
- [ ] `Admin/Tools/Edit.vue`: "Generate with AI" button functional with spinner during dispatch, flash confirmation, disabled if status = `generating`
- [ ] `Admin/Tools/Edit.vue`: "Fetch logo" button functional
- [ ] `Admin/Comparisons/Edit.vue`: "Generate with AI" button functional
- [ ] `AlternativesPanel.vue`: "Suggest alternatives" button functional, "Create tool" and "Generate" per alternative functional
- [ ] `ComparisonsPanel.vue`: "Generate comparison" per comparison functional
- [ ] Pest test: job is dispatched (Queue::fake)
- [ ] Pest test: `generating` status prevents new dispatch
- [ ] Pest test: unauthenticated access rejected
- [ ] All tests pass
- [ ] `php artisan wayfinder:generate` without error
- [ ] `vendor/bin/pint --dirty` without error
- [ ] All tests still pass

## References
- TASKS.md: T-060 to T-064

## Status: COMPLETE

**Output when complete:** `<promise>DONE</promise>`
