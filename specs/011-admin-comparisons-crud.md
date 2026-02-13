# Spec 011: Admin Comparisons CRUD

## Priority: P0

## Description
Build the complete admin CRUD for Comparisons: controller with togglePublish, form requests, routes, Vue pages (Index, Create with tool selectors, Edit with markdown content), and Pest feature tests.

## Acceptance Criteria
- [ ] Controller at `app/Http/Controllers/Admin/ComparisonController.php` with methods: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`, `togglePublish()`
- [ ] `index()`: paginated comparisons with toolA, toolB eager loaded, filterable by generation status
- [ ] `create()`: passes the list of tools for selectors
- [ ] `store()`: creates a comparison (tool_a_id, tool_b_id), slug auto-generated
- [ ] `edit()`: passes comparison with toolA, toolB
- [ ] `update()`: updates content, verdict, meta tags
- [ ] `togglePublish()`: toggles publication
- [ ] `StoreComparisonRequest`: `tool_a_id` required|exists:tools,id, `tool_b_id` required|exists:tools,id|different:tool_a_id, custom rule to verify pair does not already exist
- [ ] `UpdateComparisonRequest`: `content` nullable|string, `verdict` nullable|string, `meta_title` nullable|string|max:70, `meta_description` nullable|string|max:160
- [ ] Routes in `routes/admin.php` as resource CRUD under `/dashboard/comparatifs`
- [ ] Route `POST /dashboard/comparatifs/{comparison}/toggle-publish`
- [ ] Routes named `admin.comparisons.*`
- [ ] `Admin/Comparisons/Index.vue`: table with "Tool A vs Tool B", generation status badge, publication status, actions, admin layout
- [ ] `Admin/Comparisons/Create.vue`: two tool selectors (search/autocomplete), generated slug preview
- [ ] `Admin/Comparisons/Edit.vue`: header "Tool A vs Tool B" with logos, content textarea (markdown), verdict textarea, SEO fields, "Generate with AI" button (Phase 2), status badge, flash messages
- [ ] Pest test: store with valid pair
- [ ] Pest test: store with duplicate pair fails
- [ ] Pest test: store with tool_a_id == tool_b_id fails
- [ ] Pest test: update content and verdict
- [ ] Pest test: togglePublish
- [ ] Pest test: destroy
- [ ] All tests pass

## References
- TASKS.md: T-042 to T-046

**Output when complete:** `<promise>DONE</promise>`
