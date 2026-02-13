# Spec 014: FetchToolLogo Job

## Status: COMPLETE

## Priority: P0

## Description
Create a queued job that automatically fetches a tool's logo/favicon from its website, with fallback to Google Favicon API, and stores it locally.

## Acceptance Criteria
- [x] Job at `app/Jobs/FetchToolLogo.php`
- [x] Implements `ShouldQueue`
- [x] Logic: 1) Attempt to fetch favicon from the tool's site (parse HTML for link rel="icon" tags), 2) Fallback to Google Favicon API (`https://www.google.com/s2/favicons?domain=...&sz=128`), 3) Store file in `storage/app/public/logos/{slug}.png`, 4) Update `tool.logo_path`
- [x] Error handling: if fetch fails, log the error but do not fail the job
- [x] Retry 3 times with backoff
- [x] Timeout of 30 seconds
- [x] Pest test: favicon found in HTML -> logo stored and `logo_path` updated (mocked HTTP)
- [x] Pest test: fallback to Google Favicon API (mocked HTTP)
- [x] Pest test: HTTP error -> no crash, `logo_path` remains null
- [x] All tests pass

## References
- TASKS.md: T-051 to T-052

**Output when complete:** `<promise>DONE</promise>`
