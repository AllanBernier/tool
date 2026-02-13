# Spec 031: JSON-LD Structured Data

## Status: COMPLETE

## Priority: P1

## Description
Create a JsonLd Vue component and implement structured data (JSON-LD) schemas on all public pages: homepage, tool pages, comparison pages, category pages, and breadcrumbs.

## Acceptance Criteria
- [ ] `resources/js/components/JsonLd.vue`: props (schema object), renders `<script type="application/ld+json">` with serialized JSON, SSR-compatible (JSON-LD present in HTML source)
- [ ] Homepage: `WebSite` schema with name, url, potentialAction (SearchAction), `Organization` schema with name, url, logo, valid JSON-LD
- [ ] Tool pages: `SoftwareApplication` schema with name, url, description, applicationCategory, operatingSystem, offers (pricing), `FAQPage` schema with Q&A (if FAQ present), `BreadcrumbList` schema
- [ ] Comparison pages: `Article` schema with headline, description, datePublished, dateModified, `FAQPage` if comparison has FAQ, `BreadcrumbList` schema
- [ ] Category pages: `CollectionPage` schema with name, description, numberOfItems, `BreadcrumbList` schema
- [ ] `PublicBreadcrumbs.vue`: visual breadcrumb component with hierarchy: Accueil > [Category/Section] > [Current Page]
- [ ] Tool page: Accueil > Category > Tool
- [ ] Category page: Accueil > Categories > Category
- [ ] Comparison page: Accueil > Comparatifs > Tool A vs Tool B
- [ ] Tag page: Accueil > Tags > Tag
- [ ] Each breadcrumb has a corresponding `BreadcrumbList` JSON-LD
- [ ] Links are clickable except the current page

## References
- TASKS.md: T-107 to T-112

**Output when complete:** `<promise>DONE</promise>`
