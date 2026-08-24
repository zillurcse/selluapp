#!/usr/bin/env bash
set -e

cd /var/www/html

# Ensure storage/cache dirs exist and are writable (bind-mounted host files may
# be owned by a different uid; the "docker" user has passwordless sudo here).
sudo mkdir -p storage/framework/cache storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache 2>/dev/null || \
    mkdir -p storage/framework/cache storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache 2>/dev/null || true
sudo chmod -R a+rwX storage bootstrap/cache 2>/dev/null || \
    chmod -R a+rwX storage bootstrap/cache 2>/dev/null || true

# All Laravel config (APP_KEY, DB_*, REDIS_*, ...) comes from container env vars,
# so no .env file is required. Create a minimal one only if something expects it.
if [ ! -f .env ]; then
    (cp .env.example .env 2>/dev/null && sudo chmod a+rw .env 2>/dev/null) || \
    (sudo cp .env.example .env 2>/dev/null && sudo chmod a+rw .env 2>/dev/null) || true
fi

# Wait for the database to accept connections
if [ -n "$DB_HOST" ]; then
    echo "[entrypoint] Waiting for database at $DB_HOST:${DB_PORT:-3306}..."
    tries=0
    until php -r "exit(@fsockopen(getenv('DB_HOST'), (int)(getenv('DB_PORT') ?: 3306)) ? 0 : 1);" 2>/dev/null; do
        tries=$((tries+1))
        [ "$tries" -gt 60 ] && { echo "[entrypoint] DB wait timed out"; break; }
        sleep 2
    done
    echo "[entrypoint] Database reachable"
fi

# Run migrations only in the main php-fpm container (not the queue worker)
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "[entrypoint] Running migrations"
    php artisan migrate --force || echo "[entrypoint] migrate failed (continuing)"
fi

# Public storage symlink (ignore if it already exists)
php artisan storage:link 2>/dev/null || true

exec "$@"
