# Spec 017: GenerateComparisonContent Job

## Status: COMPLETE

## Priority: P0

## Description
Create a queued job that generates comparison content between two tools using Laravel AI.

## Acceptance Criteria
- [x] Job at `app/Jobs/GenerateComparisonContent.php`
- [x] Implements `ShouldQueue`
- [x] Receives a `Comparison` as parameter
- [x] Flow: 1) Set `generation_status` to `generating`, 2) Call API with comparison prompt (includes both tools' data as context), 3) Parse response, 4) Update `content`, `verdict`, `meta_title`, `meta_description`, 5) Status -> `completed`, `generated_at` -> now()
- [x] Error handling identical to `GenerateToolContent`
- [x] Timeout of 120 seconds
- [x] Pest test: success -> content and verdict updated, status completed (mocked API)
- [x] Pest test: failure -> status failed
- [x] All tests pass

## References
- TASKS.md: T-056 to T-057

**Output when complete:** `<promise>DONE</promise>`
