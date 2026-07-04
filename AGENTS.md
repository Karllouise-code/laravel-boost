# AGENTS.md — laravel-boost

## Stack
- **Laravel 12** + **Inertia.js 2** + **Vue 3** (Composition API) + **Vite 7** + **Tailwind CSS 4**
- No TypeScript, no ESLint — Vue SFCs use `<script setup>` with plain JS
- Tailwind 4 has no config file — configured via `@import 'tailwindcss'` in `resources/css/app.css`

## Developer Commands

| Command | What it does |
|---------|-------------|
| `composer run dev` | Runs 4 concurrent processes: `artisan serve`, `queue:listen --tries=1`, `pail --timeout=0`, `npm run dev` |
| `composer run test` | Runs `artisan config:clear` then `artisan test` (not `phpunit` directly) |
| `npm run build` | Vite production build |
| `vendor/bin/pint --dirty` | Format code (dirty-only, always run before finalizing) |
| `php artisan ziggy:generate` | Regenerate `resources/js/ziggy.js` after adding/changing routes |
| `git add -N`, `git add -p` only | Stage files for review — never commit, push, or create PRs. User does all git writes. |

## Architecture
- **Authentication via Laravel Breeze** — Login, register, password reset, email verification scaffolded. Auth middleware on todo routes. `StoreTodoRequest` still returns `authorize(): true` (any logged-in user can create). `UpdateTodoRequest` checks ownership via `user_id`.
- **No custom Artisan commands** — `app/Console/Commands/` is empty
- **Wiring file** is `bootstrap/app.php` (Laravel 11+ style), not `app/Http/Kernel.php`
- **Only custom middleware**: `HandleInertiaRequests` (appended to web group in `bootstrap/app.php`)
- **Session, cache, queue** all default to `database` driver — those tables come from the `0001_01_01_*` migrations
- **Ziggy routes cached** in `resources/js/ziggy.js` — after editing `routes/web.php`, run `php artisan ziggy:generate` to sync

## Database
| Environment | Driver | Config |
|-------------|--------|--------|
| Production (Render) | PostgreSQL (external) | Set via Render env vars (`DB_CONNECTION=pgsql`, `DB_HOST`, etc.) |
| Local dev | MySQL (configured in `.env` as `my_boosted_app`) | `.env` overrides default sqlite |
| Local fallback / Testing | SQLite | `.env.example` defaults to `sqlite`; tests use `:memory:` via `phpunit.xml` |

- `.env.example` defaults to `DB_CONNECTION=sqlite` — current `.env` uses MySQL
- Production on Render uses **free tier PostgreSQL** — creates it via Render Dashboard, then sets `DB_*` env vars on the web service
- Render requires SSL for PostgreSQL — `sslmode` defaults to `prefer` but can be overridden via `DB_SSLMODE=require`
- Lumen-style: `config('app.name')` not `env('APP_NAME')`

## Frontend
- **Pages**: `resources/js/Pages/Todos/` — `Index.vue` (Kanban), `Create.vue`, `Edit.vue`, `Show.vue`
- **Components**: `KanbanCard.vue`, `TodoCard.vue`, `ThemeToggle.vue`
- **Composable**: `useDarkMode.js` (localStorage + `prefers-color-scheme`)
- **Drag-and-drop**: `vuedraggable` on Index page — status mapped: `todo` → 0, `in_progress` → 1, `done` → 2

## Testing
- PHPUnit (not Pest) — `phpunit.xml` uses SQLite `:memory:` for all tests
- `TestCase.php` extends `Illuminate\Foundation\Testing\TestCase`
- Factories exist: `TodoFactory`, `UserFactory` — use them in tests
- `DatabaseSeeder` creates one "Test User" + 12 sample todos (4 per status)
- Currently only skeleton example tests exist

## Infrastructure
- **Dockerfile**: `php:8.2-cli` base, PHP built-in server on port 8080 (not Nginx). Installs `pgsql` + `pdo_pgsql` extensions. Runs `composer install --no-dev`, builds assets. Migrations run at **runtime** via `docker-entrypoint.sh` (supports both SQLite and PostgreSQL).
- **Startup (`docker-entrypoint.sh`)**: creates SQLite file if needed, runs `migrate --force`, caches config/routes, then execs CMD.
- **No CI** — no `.github/` workflows
- **No `.env` key** — key exists but was manually set

## Model: Todo
```
casts: completed => bool, priority => int, due_date => date
status enum: todo, in_progress, done
priority range: 1-5 (integer, used for ordering)
fillable: title, description, completed, priority, due_date, status
```
