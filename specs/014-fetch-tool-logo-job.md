# Spec 014: FetchToolLogo Job

## Priority: P0

## Description
Create a queued job that automatically fetches a tool's logo/favicon from its website, with fallback to Google Favicon API, and stores it locally.

## Acceptance Criteria
- [ ] Job at `app/Jobs/FetchToolLogo.php`
- [ ] Implements `ShouldQueue`
- [ ] Logic: 1) Attempt to fetch favicon from the tool's site (parse HTML for link rel="icon" tags), 2) Fallback to Google Favicon API (`https://www.google.com/s2/favicons?domain=...&sz=128`), 3) Store file in `storage/app/public/logos/{slug}.png`, 4) Update `tool.logo_path`
- [ ] Error handling: if fetch fails, log the error but do not fail the job
- [ ] Retry 3 times with backoff
- [ ] Timeout of 30 seconds
- [ ] Pest test: favicon found in HTML -> logo stored and `logo_path` updated (mocked HTTP)
- [ ] Pest test: fallback to Google Favicon API (mocked HTTP)
- [ ] Pest test: HTTP error -> no crash, `logo_path` remains null
- [ ] All tests pass

## References
- TASKS.md: T-051 to T-052

**Output when complete:** `<promise>DONE</promise>`
