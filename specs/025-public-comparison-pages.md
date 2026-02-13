# Spec 025: Public Comparison Pages

## Priority: P1

## Description
Create the public comparison pages: PublicComparisonController, routes, Index.vue and Show.vue pages, and Pest feature tests.

## Status: COMPLETE

## Acceptance Criteria
- [x] Controller at `app/Http/Controllers/PublicComparisonController.php`
- [x] `index()`: paginated published comparisons with toolA and toolB
- [x] `show(Comparison $comparison)`: published comparison with complete toolA and toolB data; 404 if unpublished
- [x] Routes: `GET /comparatifs` -> `comparisons.index`, `GET /comparatif/{comparison:slug}` -> `comparisons.show`
- [x] `Public/Comparisons/Index.vue`: paginated grid of `ComparisonCard`, public layout
- [x] `Public/Comparisons/Show.vue`: header (logo A + "VS" + logo B, both names), side-by-side feature comparison table (if both tools have features), detailed content (MarkdownContent), highlighted verdict section, compared pricing (two PricingTable side by side), links to individual tool pages, public layout
- [x] Pest test: show displays a published comparison (200)
- [x] Pest test: show returns 404 if unpublished
- [x] Pest test: index lists published comparisons
- [x] All tests pass

## References
- TASKS.md: T-089 to T-092

**Output when complete:** `<promise>DONE</promise>`
