# Spec 003: Category Model

## Priority: P0

## Description
Create the full Category model layer: migration, Eloquent model with relations and slug auto-generation, factory, seeder with 15 predefined dev-oriented categories, and Pest tests.

## Acceptance Criteria
- [x] Migration creates `categories` table with columns: `id` (ulid), `name` (string), `slug` (string unique), `description` (text), `icon` (string), `sort_order` (integer, default 0), `meta_title` (string nullable), `meta_description` (text nullable), `timestamps`
- [x] Unique index on `slug`
- [x] Migration executes without error
- [x] Model exists at `app/Models/Category.php`
- [x] Model uses `HasFactory` and `HasUlids` traits
- [x] Model has `hasMany(Tool::class)` relationship
- [x] `$fillable` contains all editable fields
- [x] `casts()` method defines appropriate types
- [x] Slug is auto-generated from `name` (via boot or observer)
- [x] Route model binding via `slug` for public routes
- [x] `CategoryFactory` in `database/factories/CategoryFactory.php` generates realistic data with Faker
- [x] `CategorySeeder` in `database/seeders/CategorySeeder.php` inserts 15 predefined categories: IDE & Editeurs de code, CI/CD & DevOps, Hebergement & Cloud, Bases de donnees, Monitoring & Observabilite, Gestion de projet, Communication & Collaboration, Design & Prototypage, Outils IA & Assistants de code, Securite & Tests, API & Backend, Frontend & Frameworks, Open Source, No-Code / Low-Code, Documentation & Knowledge
- [x] Each category has an auto-generated slug, relevant description, appropriate Lucide icon, and sort_order
- [x] Seeder is called in `DatabaseSeeder`
- [x] `php artisan db:seed --class=CategorySeeder` executes without error
- [x] Pest test: factory creates a valid Category
- [x] Pest test: slug is auto-generated from name
- [x] Pest test: slugs are unique (no duplicates)
- [x] Pest test: `tools()` relationship (hasMany) works
- [x] All tests pass

## References
- TASKS.md: T-005 to T-008

## Status: COMPLETE
