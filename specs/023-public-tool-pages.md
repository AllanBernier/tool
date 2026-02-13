# Spec 023: Public Tool Pages

## Status: COMPLETE

## Priority: P1

## Description
Create the public tool pages: PublicToolController, routes, Show.vue (detailed tool page with all sections), Index.vue (listing with filters and pagination), and Pest feature tests.

## Acceptance Criteria
- [x] Controller at `app/Http/Controllers/PublicToolController.php`
- [x] `index()`: paginated list of published tools with filters (category, tag, platform, text search, sort)
- [x] `show(Tool $tool)`: full tool profile with category, tags, published alternatives, published comparisons; returns 404 if unpublished
- [x] Eager loading of all necessary relations
- [x] Routes: `GET /outils` -> `tools.index`, `GET /outil/{tool:slug}` -> `tools.show`, accessible without authentication
- [x] `Public/Tools/Show.vue` using `PublicLayout`: header (large logo, name, category badge link, external site button), description section (MarkdownContent), features section (icon list), pros/cons section (ProsCons component), pricing section (PricingTable), platforms section (PlatformBadge), tags section (clickable TagBadge), FAQ section (FaqAccordion), alternatives section (ToolCard grid), comparisons section (links to VS pages), all sections conditionally displayed
- [x] `Public/Tools/Index.vue` using `PublicLayout`: title "Tous les outils", text search bar, filters (category selector, platform selector, sort: recent/alphabetical), responsive ToolCard grid, pagination (Inertia links), filters use URL query params (bookmarkable), empty state "Aucun outil trouve"
- [x] Pest test: `show` displays a published tool (200)
- [x] Pest test: `show` returns 404 for unpublished tool
- [x] Pest test: `show` returns 404 for non-existent slug
- [x] Pest test: `index` lists paginated published tools
- [x] Pest test: `index` filters by category
- [x] Pest test: `index` searches by name
- [x] All tests pass

## References
- TASKS.md: T-080 to T-084

**Output when complete:** `<promise>DONE</promise>`
