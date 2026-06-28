#!/bin/bash
# Docker entrypoint for ecommerce-eshop (Laravel frontend on Fly.io)
set -e

echo "==> Preparing Laravel storage directories..."
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         storage/app/public \
         bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "==> Bootstrapping .env from environment variables..."
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        cat > .env <<'ENV'
APP_NAME=EleganceFashionStore
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost
APP_KEY=
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en
APP_MAINTENANCE_DRIVER=database
SESSION_DRIVER=cookie
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
LOG_CHANNEL=stderr
LOG_LEVEL=warning
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elegance_shop
DB_USERNAME=root
DB_PASSWORD=
API_BASE_URL=http://localhost:8000/api/v1
API_TIMEOUT=15
API_RETRIES=2
SANCTUM_STATEFUL_DOMAINS=*
SESSION_DOMAIN=
ENV
    fi
fi

if [ -n "$APP_KEY" ]; then
    if grep -q "^APP_KEY=" .env; then
        sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env
    else
        echo "APP_KEY=$APP_KEY" >> .env
    fi
elif ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "==> Generating APP_KEY (no env var set)..."
    php artisan key:generate --force
fi

inject_env() {
    local key="$1"
    local val="$2"
    if [ -n "$val" ]; then
        if grep -q "^${key}=" .env; then
            sed -i "s|^${key}=.*|${key}=${val}|" .env
        else
            echo "${key}=${val}" >> .env
        fi
    fi
}

inject_env "APP_URL"         "$APP_URL"
inject_env "DB_HOST"         "$DB_HOST"
inject_env "DB_PORT"         "$DB_PORT"
inject_env "DB_DATABASE"     "$DB_DATABASE"
inject_env "DB_USERNAME"     "$DB_USERNAME"
inject_env "DB_PASSWORD"     "$DB_PASSWORD"
inject_env "DB_CONNECTION"   "$DB_CONNECTION"
inject_env "API_BASE_URL"    "$API_BASE_URL"
inject_env "API_TIMEOUT"     "$API_TIMEOUT"
inject_env "API_RETRIES"     "$API_RETRIES"

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

# Frontend doesn't need to run migrations (backend owns the DB schema)
# But we still cache config/routes/views
if [ "$APP_ENV" = "production" ] && [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "==> Caching config, routes, views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> Starting application..."
exec "$@"
