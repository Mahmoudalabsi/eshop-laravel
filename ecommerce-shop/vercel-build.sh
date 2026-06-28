#!/bin/bash
# Vercel build script for Laravel 12 (ecommerce-shop backend)
# Amazon Linux 2023 - uses dnf, no sudo needed (running as root).

# set -e removed to allow per-step error handling

echo "==> Diagnostics: whoami and PATH"
whoami
echo "PATH=$PATH"

# Install PHP + extensions if not available
if ! command -v php >/dev/null 2>&1; then
  echo "==> Installing PHP via dnf (Amazon Linux 2023)..."
  dnf install -y \
    php php-cli php-mbstring php-xml php-pdo php-pgsql \
    php-bcmath php-json php-zip php-curl php-gd php-intl \
    php-opcache php-process 2>&1 | tail -10
fi

PHP_BIN=$(command -v php)
echo "==> Using PHP: $PHP_BIN"
$PHP_BIN -v

# Install composer if not available
if ! command -v composer >/dev/null 2>&1; then
  echo "==> Installing composer..."
  EXPECTED_SIG=$(curl -sS https://composer.github.io/installer.sig)
  curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
  ACTUAL_SIG=$($PHP_BIN -r "echo hash_file('sha384', '/tmp/composer-setup.php');")
  if [ "$EXPECTED_SIG" != "$ACTUAL_SIG" ]; then
    echo "ERROR: Composer installer signature mismatch"
    exit 1
  fi
  $PHP_BIN /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
  rm /tmp/composer-setup.php
fi

COMPOSER_BIN=$(command -v composer)
echo "==> Using composer: $COMPOSER_BIN"

echo "==> [1/5] Composer install (production, no-dev, no-scripts)"
# Remove committed vendor/ to avoid stale dev packages causing autoloader errors
rm -rf vendor/
# --no-scripts to skip package:discover which may fail due to PHP 8.5 compat issues
$COMPOSER_BIN install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts 2>&1 || {
  echo "ERROR: Composer install failed with exit code $?"
  exit 1
}
echo "==> Composer install completed, running package:discover..."
$PHP_BIN artisan package:discover --ansi 2>&1 || echo "WARNING: package:discover failed, continuing..."

echo "==> [1.5/5] Patch Laravel SQLiteGrammar for older SQLite (Vercel PHP 8.3 has SQLite < 3.26)"
GRAMMAR_FILE="vendor/laravel/framework/src/Illuminate/Database/Schema/Grammars/SQLiteGrammar.php"
if [ -f "$GRAMMAR_FILE" ]; then
  sed -i \
    -e 's/pragma_table_xinfo(%s, %s)/pragma_table_info(%s)/g' \
    -e 's/pragma_table_xinfo(%s)/pragma_table_info(%s)/g' \
    -e 's/, hidden as "extra"/, 0 as "extra"/g' \
    "$GRAMMAR_FILE"
  if grep -q 'pragma_table_info(%s)' "$GRAMMAR_FILE"; then
    echo "Patched SQLiteGrammar: OK (using pragma_table_info with 1-arg form)"
  else
    echo "WARNING: SQLiteGrammar patch verification failed"
  fi
else
  echo "WARNING: Grammar file not found at $GRAMMAR_FILE"
fi

echo "==> [2/5] Install npm dependencies (build-only)"
# Remove package-lock.json to avoid rollup native binary bug (npm/cli#4828)
rm -rf node_modules package-lock.json
npm install --no-audit --no-fund 2>&1 || {
  echo "ERROR: npm install failed with exit code $?"
  exit 1
}
# Explicitly install rollup native binary (npm optional deps bug workaround)
npm install @rollup/rollup-linux-x64-gnu --no-save 2>&1 || echo "WARNING: could not install rollup native binary"

echo "==> [3/5] Build frontend assets (Vite)"
npm run build 2>&1 || {
  echo "ERROR: npm run build failed with exit code $?"
  exit 1
}

echo "==> [4/5] Generate APP_KEY if missing"
if [ -z "$APP_KEY" ]; then
  echo "WARNING: APP_KEY env var is not set. Generating one for this build only."
  APP_KEY="base64:$($PHP_BIN -r "echo base64_encode(random_bytes(32));")"
  export APP_KEY
fi

echo "==> [5/5] Laravel cache: config + routes + events + views (write to /tmp)"
mkdir -p /tmp/bootstrap/cache

# Verify required PHP extensions
echo "==> Verifying PHP extensions (pdo_pgsql is required for Neon)..."
$PHP_BIN -m | grep -iE "pdo_pgsql|openssl|mbstring|tokenizer|xml|ctype|json|bcmath|fileinfo|curl" || true

# Skip all artisan cache commands on Vercel serverless - they cause route resolution issues
# Laravel will compile on-demand into /tmp on first request
# $PHP_BIN artisan config:cache --no-ansi 2>&1 || echo "WARNING: config:cache failed, continuing..."
# $PHP_BIN artisan route:cache --no-ansi 2>&1 || echo "WARNING: route:cache failed, continuing..."
# $PHP_BIN artisan event:cache --no-ansi 2>&1 || echo "WARNING: event:cache failed, continuing..."
# $PHP_BIN artisan view:cache --no-ansi 2>&1 || echo "WARNING: view:cache failed, continuing..."

echo "==> Build complete."
