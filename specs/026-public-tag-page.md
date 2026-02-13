# Spec 026: Public Tag Page

## Status: COMPLETE

## Priority: P1

## Description
Create the public tag page: PublicTagController, route, Show.vue page, and Pest feature tests.

## Acceptance Criteria
- [x] Controller at `app/Http/Controllers/PublicTagController.php`
- [x] `show(Tag $tag)`: returns tag with its published tools paginated
- [x] Route: `GET /tag/{tag:slug}` -> `tags.show`
- [x] `Public/Tags/Show.vue`: header (tag name, tool count), paginated `ToolCard` grid, public layout, empty state if no tools
- [x] Pest test: show displays tag with its published tools
- [x] Pest test: show returns 404 for non-existent slug
- [x] All tests pass

## References
- TASKS.md: T-093 to T-096

**Output when complete:** `<promise>DONE</promise>`
