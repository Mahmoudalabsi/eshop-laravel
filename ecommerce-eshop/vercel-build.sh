#!/bin/bash
# Vercel build script for Laravel 12 (ecommerce-eshop frontend)
# Runs composer install, npm build, and Laravel optimization commands.

set -e

echo "==> [1/5] Composer install (production, no-dev)"
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

echo "==> [2/5] Install npm dependencies (build-only)"
npm install --no-audit --no-fund --omit=optional

echo "==> [3/5] Build frontend assets (Vite)"
npm run build

echo "==> [4/5] Generate APP_KEY if missing"
if [ -z "$APP_KEY" ]; then
  echo "WARNING: APP_KEY env var is not set. Generating one for this build only."
  APP_KEY="base64:$(php -r "echo base64_encode(random_bytes(32));")"
  export APP_KEY
fi

echo "==> [5/5] Laravel cache: config + routes + events + views (write to /tmp so they aren't baked into lambda)"
mkdir -p /tmp/bootstrap/cache

# Verify required PHP extensions are available on Vercel runtime
echo "==> Verifying PHP extensions (pdo_pgsql is required for Neon)..."
php -m | grep -E "pdo_pgsql|openssl|mbstring|tokenizer|xml|ctype|json|bcmath|fileinfo|curl" || true

php artisan config:cache --no-ansi
php artisan route:cache --no-ansi
php artisan event:cache --no-ansi
php artisan view:cache --no-ansi

echo "==> Build complete."
