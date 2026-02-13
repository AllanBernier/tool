# Spec 029: Inertia SSR

## Priority: P1

## Description
Activate and configure Inertia Server-Side Rendering (SSR) so all public pages are indexable by search engines.

## Acceptance Criteria
- [ ] SSR enabled in `config/inertia.php`
- [ ] `resources/js/ssr.ts` file exists and is configured
- [ ] `npm run build:ssr` generates the SSR bundle without error
- [ ] Node.js SSR process starts with `php artisan inertia:start-ssr`
- [ ] Public pages are server-side rendered (visible in HTML source code)
- [ ] Admin pages still function correctly

## References
- TASKS.md: T-102

**Output when complete:** `<promise>DONE</promise>`
