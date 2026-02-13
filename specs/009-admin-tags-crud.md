# Spec 009: Admin Tags CRUD

## Priority: P0

## Description
Build the complete admin CRUD for Tags: controller, form requests, routes, Vue pages (Index with pagination, Create, Edit), and Pest feature tests.

## Status: COMPLETE

## Acceptance Criteria
- [x] Controller at `app/Http/Controllers/Admin/TagController.php` with methods: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`
- [x] `index()` returns tags paginated with tools count, sorted by name
- [x] `destroy()` checks that no tools are linked before deletion
- [x] All methods use Form Requests
- [x] `StoreTagRequest` at `app/Http/Requests/Admin/StoreTagRequest.php`: `name` required|string|max:100|unique:tags
- [x] `UpdateTagRequest` at `app/Http/Requests/Admin/UpdateTagRequest.php`: same rules with uniqueness exception
- [x] Error messages in French
- [x] Routes registered in `routes/admin.php` as resource CRUD under `/dashboard/tags`
- [x] Routes named `admin.tags.*`
- [x] Protected by `auth` and `verified` middleware
- [x] `Admin/Tags/Index.vue`: paginated table with name, slug, tool count, actions (edit, delete), admin layout
- [x] `Admin/Tags/Create.vue`: simple form (name only)
- [x] `Admin/Tags/Edit.vue`: pre-filled form
- [x] Validation, flash messages on all pages
- [x] Pest test: store, update, destroy (with and without linked tools)
- [x] Pest test: index returns paginated list
- [x] Pest test: validation errors on invalid data
- [x] Pest test: unauthenticated access redirects to login
- [x] All tests pass

## References
- TASKS.md: T-028 to T-032

**Output when complete:** `<promise>DONE</promise>`
