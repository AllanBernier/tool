# Spec 032: Sitemap & Robots

## Priority: P1

## Description
Create automatic sitemap XML generation, configure robots.txt, implement SEO-friendly pagination, and write tests for SEO elements.

## Acceptance Criteria
- [ ] Route `GET /sitemap.xml` returns valid XML
- [ ] Sitemap includes: homepage, all published tool pages, all categories, all published comparisons, all tags with at least 1 tool
- [ ] Each URL has: `<loc>`, `<lastmod>` (based on updated_at), `<changefreq>`, `<priority>`
- [ ] Priorities: homepage 1.0, categories 0.8, tools 0.7, comparisons 0.7, tags 0.5
- [ ] Sitemap is cached and regenerated when content is published/modified (or via Artisan command)
- [ ] XML conforms to the Sitemap protocol
- [ ] `public/robots.txt` configured: allows all robots on public pages, blocks `/dashboard/*` routes, blocks internal API routes, references `Sitemap: {url}/sitemap.xml`
- [ ] Paginated pages have `rel="next"` and `rel="prev"` tags in `<head>`
- [ ] Pagination URLs are clean (`?page=2`)
- [ ] Paginated pages have distinct titles (append " -- Page X" if page > 1)
- [ ] Pest test: each public page returns a unique title
- [ ] Pest test: each page has a meta description
- [ ] Pest test: OG tags are present
- [ ] Pest test: sitemap XML is valid and contains correct URLs
- [ ] Pest test: robots.txt blocks correct routes
- [ ] All tests pass

## References
- TASKS.md: T-113 to T-116

**Output when complete:** `<promise>DONE</promise>`
