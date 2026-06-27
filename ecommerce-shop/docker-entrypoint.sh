#!/bin/bash
# Docker entrypoint for ecommerce-shop (Laravel backend)
set -e

echo "==> Preparing Laravel storage directories..."
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

# Ensure writable (Render may run container as root or www-data)
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
    fi
fi

# APP_KEY: use env var if set, otherwise generate
if [ -n "$APP_KEY" ]; then
    # Replace or append APP_KEY in .env (Render injects via env var, this is for safety)
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

# Run migrations only if DB is configured (skip silently if not, so the container
# can still boot and respond on /up even before the user provides real DB creds)
if [ "$DB_HOST" != "CHANGE_ME_to_aiven_or_tidb_host" ] && [ -n "$DB_HOST" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force || echo "WARNING: migrations failed (continuing)"
else
    echo "==> Skipping migrations: DB_HOST is not configured yet (placeholder value detected)"
fi

# Cache config/routes/views ONLY if DB is configured (otherwise config:cache
# would bake the broken DB credentials into a single file)
if [ "$APP_ENV" = "production" ] && [ "$DB_HOST" != "CHANGE_ME_to_aiven_or_tidb_host" ] && [ -n "$DB_HOST" ]; then
    echo "==> Caching config, routes, views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> Starting application..."
exec "$@"
