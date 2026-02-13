# Spec 017: GenerateComparisonContent Job

## Priority: P0

## Description
Create a queued job that generates comparison content between two tools using Laravel AI.

## Acceptance Criteria
- [ ] Job at `app/Jobs/GenerateComparisonContent.php`
- [ ] Implements `ShouldQueue`
- [ ] Receives a `Comparison` as parameter
- [ ] Flow: 1) Set `generation_status` to `generating`, 2) Call API with comparison prompt (includes both tools' data as context), 3) Parse response, 4) Update `content`, `verdict`, `meta_title`, `meta_description`, 5) Status -> `completed`, `generated_at` -> now()
- [ ] Error handling identical to `GenerateToolContent`
- [ ] Timeout of 120 seconds
- [ ] Pest test: success -> content and verdict updated, status completed (mocked API)
- [ ] Pest test: failure -> status failed
- [ ] All tests pass

## References
- TASKS.md: T-056 to T-057

**Output when complete:** `<promise>DONE</promise>`
