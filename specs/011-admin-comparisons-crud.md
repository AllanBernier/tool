# Spec 011: Admin Comparisons CRUD

## Priority: P0

## Status: COMPLETE

## Description
Build the complete admin CRUD for Comparisons: controller with togglePublish, form requests, routes, Vue pages (Index, Create with tool selectors, Edit with markdown content), and Pest feature tests.

## Acceptance Criteria
- [x] Controller at `app/Http/Controllers/Admin/ComparisonController.php` with methods: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`, `togglePublish()`
- [x] `index()`: paginated comparisons with toolA, toolB eager loaded, filterable by generation status
- [x] `create()`: passes the list of tools for selectors
- [x] `store()`: creates a comparison (tool_a_id, tool_b_id), slug auto-generated
- [x] `edit()`: passes comparison with toolA, toolB
- [x] `update()`: updates content, verdict, meta tags
- [x] `togglePublish()`: toggles publication
- [x] `StoreComparisonRequest`: `tool_a_id` required|exists:tools,id, `tool_b_id` required|exists:tools,id|different:tool_a_id, custom rule to verify pair does not already exist
- [x] `UpdateComparisonRequest`: `content` nullable|string, `verdict` nullable|string, `meta_title` nullable|string|max:70, `meta_description` nullable|string|max:160
- [x] Routes in `routes/admin.php` as resource CRUD under `/dashboard/comparatifs`
- [x] Route `POST /dashboard/comparatifs/{comparison}/toggle-publish`
- [x] Routes named `admin.comparisons.*`
- [x] `Admin/Comparisons/Index.vue`: table with "Tool A vs Tool B", generation status badge, publication status, actions, admin layout
- [x] `Admin/Comparisons/Create.vue`: two tool selectors (search/autocomplete), generated slug preview
- [x] `Admin/Comparisons/Edit.vue`: header "Tool A vs Tool B" with logos, content textarea (markdown), verdict textarea, SEO fields, "Generate with AI" button (Phase 2), status badge, flash messages
- [x] Pest test: store with valid pair
- [x] Pest test: store with duplicate pair fails
- [x] Pest test: store with tool_a_id == tool_b_id fails
- [x] Pest test: update content and verdict
- [x] Pest test: togglePublish
- [x] Pest test: destroy
- [x] All tests pass

## References
- TASKS.md: T-042 to T-046

**Output when complete:** `<promise>DONE</promise>`
