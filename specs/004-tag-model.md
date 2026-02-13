# Spec 004: Tag Model

## Priority: P0

## Description
Create the Tag model layer: migration, Eloquent model with relations and slug auto-generation, factory, and Pest tests.

## Acceptance Criteria
- [x] Migration creates `tags` table with columns: `id` (ulid), `name` (string), `slug` (string unique), `timestamps`
- [x] Unique index on `slug`
- [x] Migration executes without error
- [x] Model exists at `app/Models/Tag.php`
- [x] Model uses `HasFactory` and `HasUlids` traits
- [x] Model has `belongsToMany(Tool::class)` relationship
- [x] Slug is auto-generated from `name`
- [x] Route model binding via `slug` for public routes
- [x] `TagFactory` creates realistic data with Faker
- [x] Pest test: factory creates a valid Tag
- [x] Pest test: slug is auto-generated from name
- [x] Pest test: `tools()` relationship (belongsToMany) works
- [x] All tests pass

## References
- TASKS.md: T-009 to T-011

## Status: COMPLETE
