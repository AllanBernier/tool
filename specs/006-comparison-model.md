# Spec 006: Comparison Model

## Priority: P0

## Description
Create the Comparison model layer: migration, Eloquent model with relations/casts/scopes, factory, and Pest tests. Run all migrations and the CategorySeeder to initialize the database.

## Acceptance Criteria
- [ ] Migration creates `comparisons` table with columns: `id` (ulid), `tool_a_id` (foreignUlid constrained), `tool_b_id` (foreignUlid constrained), `slug` (string unique), `content` (longText nullable), `verdict` (text nullable), `generation_status` (string default 'pending'), `is_published` (boolean default false), `meta_title` (string nullable), `meta_description` (text nullable), `generated_at` (datetime nullable), `published_at` (datetime nullable), `timestamps`
- [ ] Unique index on `slug`
- [ ] Unique composite index on `(tool_a_id, tool_b_id)` to prevent duplicates
- [ ] Foreign keys constrained
- [ ] Model exists at `app/Models/Comparison.php` with `HasFactory` and `HasUlids` traits
- [ ] Relations: `belongsTo(Tool::class, 'tool_a_id')` as toolA, `belongsTo(Tool::class, 'tool_b_id')` as toolB
- [ ] Casts: `generation_status` -> `GenerationStatus::class`, `is_published` -> boolean, `generated_at` -> datetime, `published_at` -> datetime
- [ ] Slug auto-generated in format `{tool_a_slug}-vs-{tool_b_slug}`
- [ ] Scope `published()`
- [ ] `ComparisonFactory` with realistic data, `published()` state
- [ ] Route model binding via `slug`
- [ ] Pest test: factory creates valid Comparison
- [ ] Pest test: slug auto-gen in `X-vs-Y` format
- [ ] Pest test: uniqueness of the (tool_a, tool_b) pair
- [ ] Pest test: `toolA()` and `toolB()` relationships
- [ ] Pest test: `published()` scope
- [ ] `php artisan migrate:fresh --seed` executes without error
- [ ] Tables `categories`, `tags`, `tools`, `comparisons`, `tool_tag`, `tool_alternatives` all exist
- [ ] The 15 predefined categories are present in the `categories` table
- [ ] All existing tests still pass
- [ ] All tests pass

## References
- TASKS.md: T-017 to T-020

**Output when complete:** `<promise>DONE</promise>`
