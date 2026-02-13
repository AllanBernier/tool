# Spec 002: GenerationStatus Enum

## Priority: P0

## Description
Create a PHP string-backed enum `App\Enums\GenerationStatus` with values used by `Tool` and `Comparison` models to track AI content generation status.

## Acceptance Criteria
- [x] Enum file exists at `app/Enums/GenerationStatus.php`
- [x] Enum is a string-backed enum (`enum GenerationStatus: string`)
- [x] Cases: `Pending = 'pending'`, `Generating = 'generating'`, `Completed = 'completed'`, `Failed = 'failed'`
- [x] Keys use TitleCase as per project conventions

## References
- TASKS.md: T-004

## Status: COMPLETE
