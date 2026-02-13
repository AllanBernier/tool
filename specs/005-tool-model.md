# Spec 005: Tool Model

## Status: COMPLETE

## Priority: P0

## Description
Create the Tool model layer: migration for the `tools` table plus `tool_tag` and `tool_alternatives` pivot tables, Eloquent model with all relations/casts/scopes, factory, and Pest tests.

## Acceptance Criteria
- [ ] Migration creates `tools` table with columns: `id` (ulid), `name` (string), `slug` (string unique), `url` (string), `logo_path` (string nullable), `description` (text nullable), `content` (longText nullable), `pricing` (json nullable), `pros` (json nullable), `cons` (json nullable), `features` (json nullable), `faq` (json nullable), `platforms` (json nullable), `category_id` (foreignUlid, constrained), `is_published` (boolean default false), `is_sponsored` (boolean default false), `sponsored_until` (datetime nullable), `generation_status` (string default 'pending'), `meta_title` (string nullable), `meta_description` (text nullable), `generated_at` (datetime nullable), `published_at` (datetime nullable), `timestamps`
- [ ] Unique index on `slug`, foreign key on `category_id` to `categories`
- [ ] Pivot table `tool_tag` with columns: `tool_id` (foreignUlid), `tag_id` (foreignUlid), composite primary key, no timestamps
- [ ] Pivot table `tool_alternatives` with columns: `tool_id` (foreignUlid), `alternative_id` (foreignUlid), composite primary key, foreign keys to `tools`, no timestamps
- [ ] Model exists at `app/Models/Tool.php` with `HasFactory` and `HasUlids` traits
- [ ] Relations: `belongsTo(Category::class)`, `belongsToMany(Tag::class)`, `belongsToMany(Tool::class, 'tool_alternatives', 'tool_id', 'alternative_id')`, `hasMany` to Comparison (tool_a and tool_b)
- [ ] `casts()` method: `pricing` -> array, `pros` -> array, `cons` -> array, `features` -> array, `faq` -> array, `platforms` -> array, `is_published` -> boolean, `is_sponsored` -> boolean, `generation_status` -> `GenerationStatus::class`, `generated_at` -> datetime, `published_at` -> datetime, `sponsored_until` -> datetime
- [ ] Slug auto-generated from `name`
- [ ] Scope `published()`: filters `is_published = true`
- [ ] Scope `sponsored()`: filters `is_sponsored = true` and `sponsored_until > now`
- [ ] `ToolFactory` with realistic data, `published()` and `sponsored()` states
- [ ] Accessor for logo URL with fallback to placeholder
- [ ] Route model binding via `slug` for public routes
- [ ] Pest test: factory (default + published state + sponsored state)
- [ ] Pest test: slug auto-gen and uniqueness
- [ ] Pest test: `category()`, `tags()`, `alternatives()` relationships
- [ ] Pest test: `published()` and `sponsored()` scopes
- [ ] Pest test: casts (pricing returns array, generation_status returns enum, etc.)
- [ ] All tests pass

## References
- TASKS.md: T-012 to T-016

**Output when complete:** `<promise>DONE</promise>`
