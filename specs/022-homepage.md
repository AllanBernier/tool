# Spec 022: Homepage

## Priority: P1

## Description
Create the HomeController and refactor the Welcome.vue page into a full public homepage with popular tools, trending comparisons, categories, and popular tags.

## Acceptance Criteria
- [ ] Controller at `app/Http/Controllers/HomeController.php`
- [ ] `__invoke()` method returns an Inertia page with: popular tools (6-12 most recent published tools), trending comparisons (4-6 published comparisons), categories with tool count, popular tags (top 20 most used tags)
- [ ] Eager loading of relations to avoid N+1
- [ ] Route `GET /` points to this controller (replaces existing route)
- [ ] `Welcome.vue` uses `PublicLayout`
- [ ] Hero section: catchy title ("Decouvrez les meilleurs outils pour developpeurs" or similar), subtitle, CTA button to `/outils`
- [ ] Popular tools section: title + responsive grid of `ToolCard` (1 col mobile, 2 col tablet, 3 col desktop)
- [ ] Trending comparisons section: title + grid of `ComparisonCard`
- [ ] Categories section: title + grid of `CategoryCard`
- [ ] Popular tags section: cloud of clickable `TagBadge`
- [ ] Sponsor placeholder: visually reserved space (e.g., "Your tool here" section, non-functional for now)
- [ ] Dark/light mode compatible design
- [ ] Pest test: page loads (status 200)
- [ ] Pest test: published tools are displayed (drafts are not)
- [ ] Pest test: published comparisons are displayed
- [ ] Pest test: categories are displayed with count
- [ ] All tests pass

## References
- TASKS.md: T-077 to T-079

**Output when complete:** `<promise>DONE</promise>`
