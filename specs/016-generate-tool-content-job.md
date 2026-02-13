# Spec 016: GenerateToolContent Job

## Priority: P0

## Status: COMPLETE

## Description
Create a queued job that uses Laravel AI to generate the complete content for a tool profile, including description, content, pros/cons, features, FAQ, pricing, platforms, tags, and meta fields.

## Acceptance Criteria
- [x] Job at `app/Jobs/GenerateToolContent.php`
- [x] Implements `ShouldQueue`
- [x] Receives a `Tool` as parameter
- [x] Flow: 1) Set `generation_status` to `generating`, 2) Call OpenAI API via Laravel AI with structured prompt, 3) Parse JSON response, 4) Update all Tool fields (description, content, pros, cons, features, faq, pricing, platforms, meta_title, meta_description), 5) Create suggested tags if they don't exist and attach them to the tool, 6) Set `generation_status` to `completed` and `generated_at` to now(), 7) Dispatch `FetchToolLogo` and `SuggestAlternativesAndComparisons`
- [x] On error: `generation_status` -> `failed`, log the error
- [x] Retry 2 times with exponential backoff
- [x] Timeout of 120 seconds
- [x] Pest test: valid AI response -> all tool fields updated, status `completed` (mocked API)
- [x] Pest test: API error -> status `failed`, fields unchanged
- [x] Pest test: `FetchToolLogo` and `SuggestAlternativesAndComparisons` are dispatched after success
- [x] Pest test: tags are created and attached
- [x] All tests pass

## References
- TASKS.md: T-054 to T-055

**Output when complete:** `<promise>DONE</promise>`
