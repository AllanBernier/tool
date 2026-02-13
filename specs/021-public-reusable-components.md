# Spec 021: Public Reusable Components

## Priority: P1

## Description
Create all reusable Vue components for the public frontend: ToolCard, ComparisonCard, CategoryCard, TagBadge, PlatformBadge, PricingTable, FaqAccordion, ProsCons, and MarkdownContent.

## Acceptance Criteria
- [ ] `ToolCard.vue`: props (tool object), displays logo with fallback placeholder, name, category badge, truncated description, 3-4 tag badges (clickable), platform icons, links to `/outil/{slug}`, subtle hover effect (scale + shadow), responsive, "Sponsored" badge if `is_sponsored`
- [ ] `ComparisonCard.vue`: props (comparison object), displays tool A logo + "VS" + tool B logo, both tool names, short comparison description, links to `/comparatif/{slug}`, hover effect
- [ ] `CategoryCard.vue`: props (category object), displays Lucide icon, name, tool count, links to `/categorie/{slug}`, hover effect
- [ ] `TagBadge.vue`: clickable badge with tag name, links to `/tag/{slug}`, discreet style
- [ ] `PlatformBadge.vue`: badge with icon + label (Web, Desktop, Mobile, API, CLI), non-clickable, distinct style per platform
- [ ] `PricingTable.vue`: props (pricing array of plans), displays each plan with name, price, period (monthly/annual), included features, highlighted popular plan, "Free"/"Freemium"/"Paid" badge, responsive (stacked on mobile, side-by-side on desktop)
- [ ] `FaqAccordion.vue`: props (faq array of {question, answer}), collapsible/expandable items, only one open at a time (or all closed), smooth open/close animation, uses Reka UI Collapsible if appropriate
- [ ] `ProsCons.vue`: props (pros array, cons array), two columns: pros (green check icon) and cons (red x icon), responsive (side-by-side on desktop, stacked on mobile)
- [ ] `MarkdownContent.vue`: props (content string), renders markdown to secure HTML, typographic styles (headings, paragraphs, lists, code blocks, links), XSS-safe (sanitized HTML), markdown parser package installed (e.g., `marked` or `markdown-it`)

## References
- TASKS.md: T-069 to T-076

**Output when complete:** `<promise>DONE</promise>`
