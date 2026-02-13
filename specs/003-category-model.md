# Spec 003: Category Model

## Priority: P0

## Description
Create the full Category model layer: migration, Eloquent model with relations and slug auto-generation, factory, seeder with 15 predefined dev-oriented categories, and Pest tests.

## Acceptance Criteria
- [ ] Migration creates `categories` table with columns: `id` (ulid), `name` (string), `slug` (string unique), `description` (text), `icon` (string), `sort_order` (integer, default 0), `meta_title` (string nullable), `meta_description` (text nullable), `timestamps`
- [ ] Unique index on `slug`
- [ ] Migration executes without error
- [ ] Model exists at `app/Models/Category.php`
- [ ] Model uses `HasFactory` and `HasUlids` traits
- [ ] Model has `hasMany(Tool::class)` relationship
- [ ] `$fillable` contains all editable fields
- [ ] `casts()` method defines appropriate types
- [ ] Slug is auto-generated from `name` (via boot or observer)
- [ ] Route model binding via `slug` for public routes
- [ ] `CategoryFactory` in `database/factories/CategoryFactory.php` generates realistic data with Faker
- [ ] `CategorySeeder` in `database/seeders/CategorySeeder.php` inserts 15 predefined categories: IDE & Editeurs de code, CI/CD & DevOps, Hebergement & Cloud, Bases de donnees, Monitoring & Observabilite, Gestion de projet, Communication & Collaboration, Design & Prototypage, Outils IA & Assistants de code, Securite & Tests, API & Backend, Frontend & Frameworks, Open Source, No-Code / Low-Code, Documentation & Knowledge
- [ ] Each category has an auto-generated slug, relevant description, appropriate Lucide icon, and sort_order
- [ ] Seeder is called in `DatabaseSeeder`
- [ ] `php artisan db:seed --class=CategorySeeder` executes without error
- [ ] Pest test: factory creates a valid Category
- [ ] Pest test: slug is auto-generated from name
- [ ] Pest test: slugs are unique (no duplicates)
- [ ] Pest test: `tools()` relationship (hasMany) works
- [ ] All tests pass

## References
- TASKS.md: T-005 to T-008

**Output when complete:** `<promise>DONE</promise>`
