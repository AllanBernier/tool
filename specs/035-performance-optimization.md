# Spec 035: Performance Optimization

## Status: COMPLETE

## Priority: P2

## Description
Implement lazy loading for images, fix N+1 query issues, and add caching on public pages.

## Acceptance Criteria
- [ ] All `<img>` logo tags have `loading="lazy"` attribute
- [ ] Above-the-fold logos do NOT have lazy loading
- [ ] `width` and `height` attributes are defined to avoid CLS (Cumulative Layout Shift)
- [ ] Images have descriptive `alt` attributes
- [ ] Laravel strict mode enabled to detect lazy loads (`Model::preventLazyLoading()`)
- [ ] All N+1 issues fixed in public and admin controllers
- [ ] No unintentional lazy loading on public pages
- [ ] Queries are optimized (verifiable via debugbar or logs)
- [ ] Homepage data cached (popular tools, categories, trending comparisons) with 1-hour TTL
- [ ] Category list cached with 1-hour TTL
- [ ] Cache invalidated when a tool/category/comparison is published or modified
- [ ] Sitemap XML cached with invalidation on publish
- [ ] Cached data is served quickly

## References
- TASKS.md: T-120 to T-122

**Output when complete:** `<promise>DONE</promise>`
