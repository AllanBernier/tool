# Spec 024: Public Category Pages

## Priority: P1

## Description
Create the public category pages: PublicCategoryController, routes, Index.vue and Show.vue pages, and Pest feature tests.

## Acceptance Criteria
- [ ] Controller at `app/Http/Controllers/PublicCategoryController.php`
- [ ] `index()`: all categories ordered by `sort_order` with published tool count
- [ ] `show(Category $category)`: category with its published tools paginated, filterable by platform and tag, sortable
- [ ] Routes: `GET /categories` -> `categories.index`, `GET /categorie/{category:slug}` -> `categories.show`
- [ ] `Public/Categories/Index.vue`: responsive grid of `CategoryCard`, public layout
- [ ] `Public/Categories/Show.vue`: header (icon + name + description), filters (platform, tag, sort), paginated `ToolCard` grid, public layout, empty state if no tools
- [ ] Pest test: index displays categories
- [ ] Pest test: show displays category with published tools
- [ ] Pest test: show does not display unpublished tools
- [ ] All tests pass

## References
- TASKS.md: T-085 to T-088

**Output when complete:** `<promise>DONE</promise>`
