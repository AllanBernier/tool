# Tool Constitution

> Plateforme de découverte et comparaison d'outils pour développeurs. Le site permet de trouver, comparer et choisir les meilleurs outils de développement grâce à des fiches détaillées générées par IA, des comparatifs structurés, et un système de catégories/tags. Focus sur le SEO pour le référencement naturel et un design soigné (dark/light mode) responsive mobile et desktop. Le contenu est généré automatiquement via des scheduled jobs utilisant OpenAI (GPT-4o) à travers le package Laravel AI.

**Version:** 1.0.0


## Context Detection

**Ralph Loop Mode** (you're in this if started by ralph-loop.sh):
- Focus on implementation — no unnecessary questions
- Pick highest priority incomplete spec
- Complete ALL acceptance criteria
- Test thoroughly with `php artisan test --compact`
- Run `vendor/bin/pint --dirty --format agent` before committing
- Run `php artisan wayfinder:generate` if routes changed
- Commit and push
- Output `<promise>DONE</promise>` ONLY when 100% complete

**Interactive Mode** (normal conversation):
- Be helpful and conversational
- Guide decisions, create specs
- Explain Ralph loop when ready

---

## Core Principles

### I. SEO First
Le référencement naturel est la priorité absolue. Chaque page, chaque composant, chaque décision doit prendre en compte l'impact SEO. Inertia SSR activé, meta tags dynamiques, JSON-LD, sitemap XML, URLs structurées, breadcrumbs.

### II. Design Soigné
L'interface doit être belle, épurée et fonctionnelle. Dark mode par défaut avec light mode disponible. Mobile-first, responsive. Micro-animations subtiles. Chaque composant doit être visuellement abouti.

### III. Simplicité
Construire exactement ce qui est nécessaire, rien de plus. Code simple, lisible, maintenable. Pas de sur-ingénierie. Suivre les conventions Laravel et les patterns existants du projet.

---

## Technical Stack

- **Backend:** PHP 8.5.2, Laravel 12
- **Frontend:** Vue 3 + Inertia v2 + TypeScript
- **CSS:** Tailwind CSS v4
- **UI Library:** Reka UI
- **Icons:** Lucide Vue
- **SSR:** Inertia SSR (Node.js)
- **IA:** Laravel AI + OpenAI GPT-4o
- **Database:** SQLite (dev)
- **Testing:** Pest v4
- **Code Style:** Pint (PHP) + Prettier + ESLint (JS/TS)
- **Route Types:** Wayfinder
- **Auth:** Laravel Fortify (admin only)

---

## Autonomy

**YOLO Mode:** ENABLED
Full permission to read/write files, execute commands, make HTTP requests, run tests.

**Git Autonomy:** ENABLED
Commit and push without asking. Use meaningful commit messages in English. Follow conventional commit format.

---

## Project References

- **PLAN.md** — High-level architecture and design decisions
- **TASKS.md** — Detailed task list with 125 tasks, acceptance criteria, and status tracking
- **CLAUDE.md** — Laravel Boost guidelines and coding conventions

---

## Important Conventions

- **Language:** Site content in French, code/commits in English
- **Testing:** Every change must have tests. Use `php artisan test --compact --filter=TestName`
- **Formatting:** Run `vendor/bin/pint --dirty --format agent` before committing PHP
- **Routes:** Use `php artisan wayfinder:generate` after adding/changing routes
- **Models:** Use `php artisan make:model` with flags. Use factories and seeders.
- **Controllers:** Always use Form Request classes for validation
- **Frontend:** Check sibling files for conventions before creating new components
- **Database:** Use Eloquent, avoid `DB::` facade. Prevent N+1 with eager loading.

---

## Work Items

The agent discovers work dynamically from:
1. **specs/ folder** — Primary source, look for incomplete `.md` files
2. **TASKS.md** — Reference for task details and acceptance criteria

Create specs using `/speckit.specify [description]` or manually create `specs/NNN-feature-name.md`.

Each spec MUST have **testable acceptance criteria**.

### Re-Verification Mode

When all specs appear complete, the agent will:
1. Randomly pick a completed spec
2. Strictly re-verify ALL acceptance criteria
3. Fix any regressions found
4. Only output `<promise>DONE</promise>` if quality confirmed

---

## Running Ralph

```bash
# Claude Code
./scripts/ralph-loop.sh

# OpenAI Codex
./scripts/ralph-loop-codex.sh

# With iteration limit
./scripts/ralph-loop.sh 20
```

---

## Completion Signal

When a spec is 100% complete:
1. All acceptance criteria verified
2. Tests pass (`php artisan test --compact`)
3. Code formatted (`vendor/bin/pint --dirty --format agent`)
4. Changes committed and pushed
5. Output: `<promise>DONE</promise>`

**Never output this until truly complete.**
