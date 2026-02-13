# Spec 013: Configure Laravel AI

## Priority: P0

## Description
Configure the Laravel AI package to use GPT-4o as the default model with the OpenAI provider.

## Acceptance Criteria
- [ ] `config/ai.php` configured with the OpenAI provider and model `gpt-4o`
- [ ] API key is read from `OPENAI_API_KEY` in `.env`
- [ ] A quick test via tinker confirms the API responds correctly
- [ ] Laravel AI documentation is consulted to ensure proper configuration

## References
- TASKS.md: T-050

**Output when complete:** `<promise>DONE</promise>`
