# Spec 020: Public Layout

## Status: COMPLETE

## Priority: P1

## Description
Create the public-facing layout with header, footer, and mobile menu components, distinct from the admin sidebar layout.

## Acceptance Criteria
- [x] `resources/js/layouts/PublicLayout.vue` with structure: `<header>` + `<main>` (slot) + `<footer>`
- [x] Responsive (mobile-first)
- [x] Supports dark/light mode via existing appearance system
- [x] Usable by public pages via `layout: PublicLayout` in page `defineOptions`
- [x] `PublicHeader.vue`: clickable "Tool" logo (home link), navigation links ("Outils", "Categories", "Comparatifs"), search button with icon + "Cmd+K" label, dark/light mode toggle, hamburger menu on mobile, active link visually distinct, clean responsive design
- [x] `PublicFooter.vue`: grid of links to main categories, section links (Accueil, Outils, Categories, Comparatifs), copyright and legal mentions, responsive (columns on desktop, stacked on mobile)
- [x] `MobileMenu.vue`: opens via hamburger button in header, displays all navigation links, smooth open/close animation, closes on link click or click outside, full screen height

## References
- TASKS.md: T-065 to T-068

**Output when complete:** `<promise>DONE</promise>`
