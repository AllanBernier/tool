# Spec 018: SuggestAlternativesAndComparisons Job

## Priority: P0

## Description
Create a queued job that asks the AI to suggest alternatives for a tool and creates the corresponding Tool and Comparison entries.

## Acceptance Criteria
- [ ] Job at `app/Jobs/SuggestAlternativesAndComparisons.php`
- [ ] Implements `ShouldQueue`
- [ ] Receives a `Tool` as parameter
- [ ] Flow: 1) Call AI API with suggestion prompt, 2) For each suggested alternative: check if Tool exists by name, otherwise create a draft Tool (name + url if provided + inferred category_id, generation_status: pending), 3) Attach as alternative (pivot table `tool_alternatives`), 4) For each alternative: create a Comparison if the pair doesn't exist (generation_status: pending)
- [ ] Does not recreate already existing alternatives
- [ ] Logs the number of alternatives and comparisons created
- [ ] Pest test: creates draft Tools for non-existing alternatives (mocked API)
- [ ] Pest test: does not duplicate already existing alternatives
- [ ] Pest test: creates Comparisons for each pair
- [ ] Pest test: does not duplicate existing Comparisons
- [ ] All tests pass

## References
- TASKS.md: T-058 to T-059

**Output when complete:** `<promise>DONE</promise>`
