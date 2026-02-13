# Spec 004: Tag Model

## Priority: P0

## Description
Create the Tag model layer: migration, Eloquent model with relations and slug auto-generation, factory, and Pest tests.

## Acceptance Criteria
- [ ] Migration creates `tags` table with columns: `id` (ulid), `name` (string), `slug` (string unique), `timestamps`
- [ ] Unique index on `slug`
- [ ] Migration executes without error
- [ ] Model exists at `app/Models/Tag.php`
- [ ] Model uses `HasFactory` and `HasUlids` traits
- [ ] Model has `belongsToMany(Tool::class)` relationship
- [ ] Slug is auto-generated from `name`
- [ ] Route model binding via `slug` for public routes
- [ ] `TagFactory` creates realistic data with Faker
- [ ] Pest test: factory creates a valid Tag
- [ ] Pest test: slug is auto-generated from name
- [ ] Pest test: `tools()` relationship (belongsToMany) works
- [ ] All tests pass

## References
- TASKS.md: T-009 to T-011

**Output when complete:** `<promise>DONE</promise>`
