#!/bin/bash
# Docker entrypoint for ecommerce-shop (Laravel backend)
set -e

# Ensure storage directories exist and are writable
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

# Copy .env.example if .env doesn't exist (Render/Railway set env vars via dashboard)
if [ ! -f .env ]; then
    cp .env.production.example .env 2>/dev/null || cp .env.example .env
fi

# Generate APP_KEY if not set
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force
fi

# Run migrations (safe — uses --force to skip prompt in production)
php artisan migrate --force

# Create storage symlink
php artisan storage:link 2>/dev/null || true

# Cache config and routes for performance (only in production)
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Execute the main command
exec "$@"
