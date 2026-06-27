#!/bin/bash
# Docker entrypoint for ecommerce-eshop (Laravel frontend)
set -e

# Ensure storage directories exist and are writable
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

# Copy .env.example if .env doesn't exist
if [ ! -f .env ]; then
    cp .env.production.example .env 2>/dev/null || cp .env.example .env
fi

# Generate APP_KEY if not set
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force
fi

# NOTE: We do NOT run migrations here. The ecommerce-shop (Backend) handles all DB migrations.
# Run `php artisan migrate` from inside ecommerce-shop only.

# Create storage symlink (for profile images etc. - though these are primarily stored by the backend)
php artisan storage:link 2>/dev/null || true

# Cache config and routes for performance
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec "$@"
