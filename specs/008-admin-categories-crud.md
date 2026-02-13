# Spec 008: Admin Categories CRUD

## Priority: P0

## Status: COMPLETE

## Description
Build the complete admin CRUD for Categories: controller with all methods, form requests for validation, routes, Vue pages (Index with drag-and-drop reorder, Create, Edit), and Pest feature tests.

## Acceptance Criteria
- [x] Controller at `app/Http/Controllers/Admin/CategoryController.php` with methods: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`, `reorder()`
- [x] `index()` returns categories ordered by `sort_order` with tools count
- [x] `store()` creates a category and redirects to index
- [x] `update()` updates and redirects
- [x] `destroy()` deletes (with check that no tools are linked)
- [x] `reorder()` accepts an array of ordered IDs and updates `sort_order`
- [x] All methods use Form Requests
- [x] `StoreCategoryRequest` at `app/Http/Requests/Admin/StoreCategoryRequest.php`: `name` required|string|max:255|unique:categories, `description` required|string, `icon` required|string|max:50
- [x] `UpdateCategoryRequest` at `app/Http/Requests/Admin/UpdateCategoryRequest.php`: same rules with `unique:categories,name,{id}` exception
- [x] Custom error messages in French
- [x] Validation rules follow existing project conventions (string or array format)
- [x] `routes/admin.php` created and included in `routes/web.php`
- [x] Routes protected by `auth` and `verified` middleware, prefixed with `/dashboard`
- [x] Routes: `GET /dashboard/categories` (index), `GET /dashboard/categories/create` (create), `POST /dashboard/categories` (store), `GET /dashboard/categories/{category}/edit` (edit), `PUT /dashboard/categories/{category}` (update), `DELETE /dashboard/categories/{category}` (destroy), `POST /dashboard/categories/reorder` (reorder)
- [x] Routes named `admin.categories.*`
- [x] `Admin/Categories/Index.vue`: table with columns (icon, name, slug, tool count, actions), "Add category" button, Edit/Delete actions per row with confirmation, drag-and-drop reorder updating `sort_order` via `reorder` endpoint, admin layout, flash messages
- [x] `Admin/Categories/Create.vue`: form with name, description, icon (Lucide icon selector), validation, error display, Cancel/Save buttons, Inertia form helper, collapsible SEO section (meta_title, meta_description)
- [x] `Admin/Categories/Edit.vue`: same form pre-filled with existing data
- [x] Pest test: `index` returns category list (authenticated)
- [x] Pest test: `index` redirects to login if unauthenticated
- [x] Pest test: `store` creates a category with valid data
- [x] Pest test: `store` fails with invalid data (validation)
- [x] Pest test: `update` updates an existing category
- [x] Pest test: `destroy` deletes a category with no linked tools
- [x] Pest test: `destroy` fails if category has linked tools
- [x] Pest test: `reorder` updates category order
- [x] All tests pass

## References
- TASKS.md: T-022 to T-027

**Output when complete:** `<promise>DONE</promise>`
