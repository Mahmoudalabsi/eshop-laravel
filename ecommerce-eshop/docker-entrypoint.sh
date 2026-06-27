#!/bin/bash
# Docker entrypoint for ecommerce-eshop (Laravel frontend - storefront)
set -e

echo "==> Preparing Laravel storage directories..."
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "==> Bootstrapping .env..."
if [ ! -f .env ]; then
    if [ -f .env.production.example ]; then
        cp .env.production.example .env
    elif [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "APP_NAME=EleganceFashion" > .env
        echo "APP_ENV=production" >> .env
        echo "APP_DEBUG=false" >> .env
        echo "API_BASE_URL=https://elegance-fashion-api.onrender.com/api/v1" >> .env
    fi
fi

if [ -n "$APP_KEY" ]; then
    if grep -q "^APP_KEY=" .env; then
        sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env
    else
        echo "APP_KEY=$APP_KEY" >> .env
    fi
elif ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force
fi

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

# NOTE: Frontend doesn't run migrations - backend (elegance-fashion-api) owns the DB schema.
# Skip config:cache when DB is not configured to avoid baking broken creds.
if [ "$APP_ENV" = "production" ] && [ "$DB_HOST" != "CHANGE_ME_to_aiven_or_tidb_host" ] && [ -n "$DB_HOST" ]; then
    echo "==> Caching config, routes, views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> Starting application..."
exec "$@"
