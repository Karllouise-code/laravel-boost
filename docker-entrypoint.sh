#!/bin/sh
set -e

# If using SQLite, ensure the database file exists
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    touch database/database.sqlite
fi

# Run migrations (safe to run every time — Laravel's Migrator tracks what's been run)
php artisan migrate --force

# Clear and rebuild cache for production
php artisan config:cache --quiet 2>/dev/null || true
php artisan route:cache --quiet 2>/dev/null || true

exec "$@"
