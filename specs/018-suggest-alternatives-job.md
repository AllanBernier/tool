# Spec 018: SuggestAlternativesAndComparisons Job

## Status: COMPLETE

## Priority: P0

## Description
Create a queued job that asks the AI to suggest alternatives for a tool and creates the corresponding Tool and Comparison entries.

## Acceptance Criteria
- [x] Job at `app/Jobs/SuggestAlternativesAndComparisons.php`
- [x] Implements `ShouldQueue`
- [x] Receives a `Tool` as parameter
- [x] Flow: 1) Call AI API with suggestion prompt, 2) For each suggested alternative: check if Tool exists by name, otherwise create a draft Tool (name + url if provided + inferred category_id, generation_status: pending), 3) Attach as alternative (pivot table `tool_alternatives`), 4) For each alternative: create a Comparison if the pair doesn't exist (generation_status: pending)
- [x] Does not recreate already existing alternatives
- [x] Logs the number of alternatives and comparisons created
- [x] Pest test: creates draft Tools for non-existing alternatives (mocked API)
- [x] Pest test: does not duplicate already existing alternatives
- [x] Pest test: creates Comparisons for each pair
- [x] Pest test: does not duplicate existing Comparisons
- [x] All tests pass

## References
- TASKS.md: T-058 to T-059

**Output when complete:** `<promise>DONE</promise>`
