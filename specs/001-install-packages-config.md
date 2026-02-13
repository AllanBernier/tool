# Spec 001: Install Packages & Configuration

## Priority: P0

## Description
Install the `laravel/ai` package, configure a dedicated filesystem disk for tool logos, and configure the queue driver for background job processing.

## Acceptance Criteria
- [ ] `composer require laravel/ai` executes without error
- [ ] `config/ai.php` is published
- [ ] `OPENAI_API_KEY` is added to `.env` and `.env.example`
- [ ] The OpenAI provider is configured as the default provider
- [ ] A `logos` disk is configured in `filesystems.php` pointing to `storage/app/public/logos`
- [ ] The directory `storage/app/public/logos/` exists
- [ ] `php artisan storage:link` works correctly
- [ ] Logos are publicly accessible via `/storage/logos/`
- [ ] `QUEUE_CONNECTION=database` is set in `.env`
- [ ] The `jobs` table migration exists
- [ ] `php artisan queue:work` starts without error

## References
- TASKS.md: T-001 to T-003

**Output when complete:** `<promise>DONE</promise>`
