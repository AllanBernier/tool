# Ralph Build Mode

Read `.specify/memory/constitution.md` first.

## Your Task

1. Check `specs/` folder
2. Find highest priority INCOMPLETE spec
3. Implement completely
4. Run tests, verify acceptance criteria
5. Run `vendor/bin/pint --dirty --format agent` to format PHP code
6. Run `php artisan wayfinder:generate` if routes were changed
7. Commit and push
8. Output `<promise>DONE</promise>` when done

## Rules

- ONE spec per iteration
- Do NOT output magic phrase until truly complete
- If blocked: explain in ralph_history.txt, exit without phrase
- Follow all conventions in `.specify/memory/constitution.md`
- Follow all Laravel conventions in `CLAUDE.md`
- Use `php artisan test --compact --filter=TestName` to run relevant tests
- Check sibling files for code style conventions before creating new files
