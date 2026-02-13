# Spec 015: AI Prompt Templates

## Priority: P0

## Description
Create structured AI prompt templates for generating tool content, comparison content, and alternative suggestions. Store them in dedicated PHP files.

## Acceptance Criteria
- [ ] `app/AI/Prompts/GenerateToolPrompt.php` (or config/prompts): system prompt + user prompt for generating a complete tool profile
- [ ] Tool prompt requests: description (~150 chars), content (~500-1000 words in markdown, SEO-optimized, in French), pros list (5-8), cons list (3-5), main features (5-10), FAQ (5-8 Q&A), pricing (plan structure), supported platforms, suggested tags, suggested alternatives (5-10 tool names), meta_title (~60 chars), meta_description (~155 chars)
- [ ] Prompt specifies expected JSON output format
- [ ] Prompt specifies tone: professional, developer-oriented, objective
- [ ] `app/AI/Prompts/GenerateComparisonPrompt.php`: prompt for comparisons
- [ ] `app/AI/Prompts/SuggestAlternativesPrompt.php`: prompt for suggestions

## References
- TASKS.md: T-053

**Output when complete:** `<promise>DONE</promise>`
