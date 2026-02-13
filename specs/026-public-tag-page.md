# Spec 026: Public Tag Page

## Priority: P1

## Description
Create the public tag page: PublicTagController, route, Show.vue page, and Pest feature tests.

## Acceptance Criteria
- [ ] Controller at `app/Http/Controllers/PublicTagController.php`
- [ ] `show(Tag $tag)`: returns tag with its published tools paginated
- [ ] Route: `GET /tag/{tag:slug}` -> `tags.show`
- [ ] `Public/Tags/Show.vue`: header (tag name, tool count), paginated `ToolCard` grid, public layout, empty state if no tools
- [ ] Pest test: show displays tag with its published tools
- [ ] Pest test: show returns 404 for non-existent slug
- [ ] All tests pass

## References
- TASKS.md: T-093 to T-096

**Output when complete:** `<promise>DONE</promise>`
