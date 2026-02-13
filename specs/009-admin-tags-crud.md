# Spec 009: Admin Tags CRUD

## Priority: P0

## Description
Build the complete admin CRUD for Tags: controller, form requests, routes, Vue pages (Index with pagination, Create, Edit), and Pest feature tests.

## Acceptance Criteria
- [ ] Controller at `app/Http/Controllers/Admin/TagController.php` with methods: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`
- [ ] `index()` returns tags paginated with tools count, sorted by name
- [ ] `destroy()` checks that no tools are linked before deletion
- [ ] All methods use Form Requests
- [ ] `StoreTagRequest` at `app/Http/Requests/Admin/StoreTagRequest.php`: `name` required|string|max:100|unique:tags
- [ ] `UpdateTagRequest` at `app/Http/Requests/Admin/UpdateTagRequest.php`: same rules with uniqueness exception
- [ ] Error messages in French
- [ ] Routes registered in `routes/admin.php` as resource CRUD under `/dashboard/tags`
- [ ] Routes named `admin.tags.*`
- [ ] Protected by `auth` and `verified` middleware
- [ ] `Admin/Tags/Index.vue`: paginated table with name, slug, tool count, actions (edit, delete), admin layout
- [ ] `Admin/Tags/Create.vue`: simple form (name only)
- [ ] `Admin/Tags/Edit.vue`: pre-filled form
- [ ] Validation, flash messages on all pages
- [ ] Pest test: store, update, destroy (with and without linked tools)
- [ ] Pest test: index returns paginated list
- [ ] Pest test: validation errors on invalid data
- [ ] Pest test: unauthenticated access redirects to login
- [ ] All tests pass

## References
- TASKS.md: T-028 to T-032

**Output when complete:** `<promise>DONE</promise>`
