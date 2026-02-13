# Spec 023: Public Tool Pages

## Priority: P1

## Description
Create the public tool pages: PublicToolController, routes, Show.vue (detailed tool page with all sections), Index.vue (listing with filters and pagination), and Pest feature tests.

## Acceptance Criteria
- [ ] Controller at `app/Http/Controllers/PublicToolController.php`
- [ ] `index()`: paginated list of published tools with filters (category, tag, platform, text search, sort)
- [ ] `show(Tool $tool)`: full tool profile with category, tags, published alternatives, published comparisons; returns 404 if unpublished
- [ ] Eager loading of all necessary relations
- [ ] Routes: `GET /outils` -> `tools.index`, `GET /outil/{tool:slug}` -> `tools.show`, accessible without authentication
- [ ] `Public/Tools/Show.vue` using `PublicLayout`: header (large logo, name, category badge link, external site button), description section (MarkdownContent), features section (icon list), pros/cons section (ProsCons component), pricing section (PricingTable), platforms section (PlatformBadge), tags section (clickable TagBadge), FAQ section (FaqAccordion), alternatives section (ToolCard grid), comparisons section (links to VS pages), all sections conditionally displayed
- [ ] `Public/Tools/Index.vue` using `PublicLayout`: title "Tous les outils", text search bar, filters (category selector, platform selector, sort: recent/alphabetical), responsive ToolCard grid, pagination (Inertia links), filters use URL query params (bookmarkable), empty state "Aucun outil trouve"
- [ ] Pest test: `show` displays a published tool (200)
- [ ] Pest test: `show` returns 404 for unpublished tool
- [ ] Pest test: `show` returns 404 for non-existent slug
- [ ] Pest test: `index` lists paginated published tools
- [ ] Pest test: `index` filters by category
- [ ] Pest test: `index` searches by name
- [ ] All tests pass

## References
- TASKS.md: T-080 to T-084

**Output when complete:** `<promise>DONE</promise>`
