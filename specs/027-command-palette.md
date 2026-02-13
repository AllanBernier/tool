# Spec 027: Command Palette

## Priority: P1

## Description
Create the search API endpoint and the CommandPalette.vue component with keyboard shortcut support for quick search across tools, categories, and tags.

## Status: COMPLETE

## Acceptance Criteria
- [x] Controller at `app/Http/Controllers/SearchController.php`
- [x] `__invoke(Request $request)`: receives a `query` string, returns JSON with grouped results
- [x] Searches in: published tools (by name, description), categories (by name), tags (by name)
- [x] Results limited (max 5 per type)
- [x] Each result contains: type, name, slug, url, description/additional info
- [x] Route `GET /api/search` or `GET /search`
- [x] `CommandPalette.vue`: opens with Cmd+K (Mac) / Ctrl+K (Windows/Linux), closes with Escape or click outside, auto-focused search input, debounced API call (300ms) on each keystroke, results grouped by type (Outils, Categories, Tags) with icon per type, keyboard navigation (up/down arrows to select, Enter to navigate), click on result navigates and closes modal, empty state "Tapez pour rechercher", no results state "Aucun resultat pour {query}", loading spinner during search, dark overlay behind modal, open/close animation, integrated in `PublicLayout` (always available)
- [x] Pest test: search by tool name returns result
- [x] Pest test: search by category name returns result
- [x] Pest test: empty search returns empty results
- [x] Pest test: only published tools appear
- [x] All tests pass

## References
- TASKS.md: T-097 to T-099

**Output when complete:** `<promise>DONE</promise>`
