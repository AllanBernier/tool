# Spec 029: Inertia SSR

## Priority: P1

## Status: COMPLETE

## Description
Activate and configure Inertia Server-Side Rendering (SSR) so all public pages are indexable by search engines.

## Acceptance Criteria
- [x] SSR enabled in `config/inertia.php`
- [x] `resources/js/ssr.ts` file exists and is configured
- [x] `npm run build:ssr` generates the SSR bundle without error
- [x] Node.js SSR process starts with `php artisan inertia:start-ssr`
- [x] Public pages are server-side rendered (visible in HTML source code)
- [x] Admin pages still function correctly

## References
- TASKS.md: T-102

**Output when complete:** `<promise>DONE</promise>`
