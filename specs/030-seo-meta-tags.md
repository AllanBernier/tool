# Spec 030: SEO Meta Tags

## Priority: P1

## Description
Create a centralized SEO service for managing dynamic meta tags, implement meta tags on all public pages, add Open Graph and Twitter Card tags, and implement canonical URLs.

## Acceptance Criteria
- [ ] `app/Services/SeoMeta.php` class (or Inertia sharing approach) with methods to define: title, description, canonical URL, OG tags, Twitter card tags
- [ ] Intelligent fallbacks: no custom meta_title -> auto-generated title, no meta_description -> truncated from description
- [ ] `HandleInertiaRequests` middleware updated to share meta tags with all pages
- [ ] Title format: "{Page Title} -- Tool" (with separator and site name)
- [ ] Homepage: title "Tool -- Decouvrez les meilleurs outils pour developpeurs", relevant description
- [ ] Tool page: title = meta_title or "{name} -- Avis, Prix, Alternatives | Tool", description = meta_description or truncated description
- [ ] Category page: title = "{name} -- Meilleurs outils | Tool", description = category description
- [ ] Comparison page: title = meta_title or "{Tool A} vs {Tool B} -- Comparatif | Tool", adapted description
- [ ] Tag page: title = "Outils {tag} | Tool"
- [ ] Listing pages: appropriate titles and descriptions
- [ ] Each page has unique `<title>` and `<meta name="description">`
- [ ] Meta tags visible in HTML source (via SSR)
- [ ] OG tags on every public page: `og:title`, `og:description`, `og:url`, `og:type` (website for homepage, article for detail pages), `og:image` (tool logo or default site image)
- [ ] Twitter tags: `twitter:card` (summary_large_image), `twitter:title`, `twitter:description`, `twitter:image`
- [ ] Default OG image at `public/images/og-default.png`
- [ ] Tool pages: `og:image` uses tool logo if available
- [ ] Each public page has a `<link rel="canonical">` tag with absolute URL
- [ ] Paginated pages have canonical to page 1 (or current page per strategy)
- [ ] No query parameters in canonicals (except pagination if needed)

## References
- TASKS.md: T-103 to T-106

**Output when complete:** `<promise>DONE</promise>`
