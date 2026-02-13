# Spec 028: Responsive Design

## Priority: P1

## Description
Verify and adjust responsive design on all public pages for mobile, tablet, and desktop. Regenerate Wayfinder and format with Pint.

## Acceptance Criteria
- [ ] Homepage: hero, grids, tags adapt on mobile (1 column)
- [ ] Tool page: all sections readable on mobile, pros/cons stacked, pricing stacked
- [ ] Category page: grid adapted, filters stacked on mobile
- [ ] Comparison page: table horizontally scrollable or stacked on mobile
- [ ] Command palette: full width on mobile
- [ ] Header: functional hamburger menu on mobile
- [ ] Footer: columns stacked on mobile
- [ ] No horizontal overflow on any page
- [ ] Text readable (minimum 16px on mobile)
- [ ] `php artisan wayfinder:generate` without error
- [ ] `vendor/bin/pint --dirty` without error
- [ ] All tests pass

## References
- TASKS.md: T-100 to T-101

**Output when complete:** `<promise>DONE</promise>`
