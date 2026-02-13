# Spec 010: Admin Tools CRUD

## Priority: P0

## Description
Build the complete admin CRUD for Tools: controller with all methods including togglePublish, form requests, routes, Vue pages (Index with filters/pagination, Create, Edit with full content editing, AlternativesPanel, ComparisonsPanel), and Pest feature tests.

## Acceptance Criteria
- [ ] Controller at `app/Http/Controllers/Admin/ToolController.php` with methods: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`, `togglePublish()`
- [ ] `index()`: paginated tools with category, filterable by category and generation status, searchable by name
- [ ] `create()`: passes available categories and tags
- [ ] `store()`: creates a tool with base fields (name, url, category_id) + optional tags
- [ ] `show()`: returns tool with all relations (category, tags, alternatives, comparisons)
- [ ] `edit()`: passes tool, categories, tags
- [ ] `update()`: updates tool with tag and alternative sync
- [ ] `destroy()`: deletes tool and its pivot relations
- [ ] `togglePublish()`: toggles `is_published` and updates `published_at`
- [ ] `StoreToolRequest`: `name` required|string|max:255|unique:tools, `url` required|url|max:500, `category_id` required|exists:categories,id, `tags` nullable|array, `tags.*` exists:tags,id
- [ ] `UpdateToolRequest`: same rules with uniqueness exception + optional content fields (description, content, pricing, pros, cons, features, faq, platforms, meta_title, meta_description), JSON field rules (nullable|array)
- [ ] Routes registered in `routes/admin.php` as resource CRUD under `/dashboard/outils`
- [ ] Route `POST /dashboard/outils/{tool}/toggle-publish` (togglePublish)
- [ ] Routes named `admin.tools.*`
- [ ] `Admin/Tools/Index.vue`: table with logo thumbnail, name, category, publication status badge, generation status badge (colored), creation date, actions (View, Edit, Publish/Unpublish, Delete), category filter, generation status filter, text search by name, pagination, admin layout
- [ ] `Admin/Tools/Create.vue`: form with name, url, category_id (select), tags (multi-select), category selector, validation, Inertia form helper, redirect to edit page after creation
- [ ] `Admin/Tools/Edit.vue`: full editing form with sections for base fields, short description (textarea), full content (markdown textarea with preview), features (dynamic list), pros (dynamic list), cons (dynamic list), pricing (structured JSON editor), platforms (checkboxes: Web, Desktop, Mobile, API, CLI), FAQ (dynamic question/answer pairs), collapsible SEO section, status section with badges and publish/unpublish button, "Generate with AI" button (wired in Phase 2), logo preview with placeholder fallback, Inertia form helper
- [ ] `Admin/Tools/AlternativesPanel.vue`: lists tool alternatives with status badges (published/draft/generating/to create), "Create tool" button for non-existing alternatives, "Generate with AI" button for ungenerated alternatives (Phase 2), "Suggest alternatives" button if none exist (Phase 2)
- [ ] `Admin/Tools/ComparisonsPanel.vue`: lists existing and potential comparisons with status badges, "Generate comparison" button for ungenerated comparisons (Phase 2), empty state message
- [ ] Pest test: `index` with paginated list, category filter, status filter
- [ ] Pest test: `store` creates tool with valid data and syncs tags
- [ ] Pest test: `store` validation (unique name, valid url, category_id exists)
- [ ] Pest test: `update` updates tool and syncs tags
- [ ] Pest test: `destroy` deletes tool and its pivots
- [ ] Pest test: `togglePublish` toggles publication
- [ ] Pest test: unauthenticated access redirected
- [ ] All tests pass

## References
- TASKS.md: T-033 to T-041

**Output when complete:** `<promise>DONE</promise>`
