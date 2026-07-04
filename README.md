# Boosted Todo App

A themed Kanban board todo app with Laravel 12, Inertia.js 2, Vue 3, and Tailwind CSS 4. Features Breeze authentication, per-user todo scoping, and dual coffee/nes themes.

## Features

- **Kanban Board** — Drag-and-drop columns: To Do, In Progress, Done
- **Authentication** — Login, register, password reset, email verification (Breeze/Inertia stack)
- **Per-User Scoping** — Each user sees only their own todos
- **Dual Themes** — Coffee (default dark) and NES (retro purple/pink) with CSS variables
- **Priority System** — 5-level priority with color-coded badges
- **Due Date Tracking** — Set due dates with overdue indicators
- **Download SQLite** — Export your local database via the Kanban board
- **Responsive** — Works on desktop and mobile

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Vue 3 (Composition API), Inertia.js 2 |
| Styling | Tailwind CSS 4, CSS variables for theming |
| Build | Vite 7 |
| Database | SQLite (local/testing), MySQL (local dev), PostgreSQL (production) |
| Auth | Laravel Breeze |
| Drag & Drop | vuedraggable |

## Quick Start

### Prerequisites

- PHP 8.2+, Node 18+, Composer
- [Herd](https://herd.laravel.com) (recommended) or XAMPP for local dev
- MySQL via DBngin (or use SQLite by setting `DB_CONNECTION=sqlite`)

### Installation

```bash
git clone <repository-url>
cd laravel-boost
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Database Setup

For MySQL (default local):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_boosted_app
DB_USERNAME=root
DB_PASSWORD=
```

For SQLite (testing / fallback):

```env
DB_CONNECTION=sqlite
```

### Run Migrations & Seed

```bash
php artisan migrate
php artisan db:seed
```

This creates two test users (password: `password`):
- `test@example.com`
- `user2@example.com`

### Build & Serve

```bash
npm run build
php artisan serve
```

Visit `http://127.0.0.1:8000` — you'll be redirected to login.

## Development Commands

```bash
composer run dev        # Start all dev processes (artisan serve, queue, pail, npm run dev)
composer run test       # Run test suite (config:clear + php artisan test)
npm run build           # Vite production build
vendor/bin/pint --dirty # Format PHP (dirty only)
php artisan ziggy:generate  # Refresh JS routes after changing routes/web.php
```

## Database Schema

### Todos

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| user_id | bigint | FK → users, NOT NULL |
| title | string | Required |
| description | text | Nullable |
| status | enum | `todo`, `in_progress`, `done` |
| priority | integer | 1–5 |
| completed | boolean | |
| due_date | date | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### Users

Standard Laravel `users` table with `name`, `email`, `password`, plus `email_verified_at` and `remember_token`.

## Themes

Toggle between **Coffee** (warm dark) and **NES** (retro purple) via the button in the nav bar. Theme preference persists in localStorage. CSS variables (`--color-*`) control all colors across auth pages, layouts, and the Kanban board.

## Deployment (Render)

The `Dockerfile` builds for Render's free tier:

1. Set `DB_CONNECTION=pgsql` and PostgreSQL credentials via Render env vars
2. Migrations run at startup via `docker-entrypoint.sh`
3. Assets are built during `docker build`

```env
# Render env vars
DB_CONNECTION=pgsql
DB_HOST=<render-postgres-host>
DB_PORT=5432
DB_DATABASE=<db-name>
DB_USERNAME=<db-user>
DB_PASSWORD=<db-password>
```

## License

MIT
