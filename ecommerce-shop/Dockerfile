# Simplified Dockerfile for ecommerce-shop (Backend - API + Admin)
# Uses multi-stage build: Node 20 for assets, PHP 8.2 for runtime
# Designed for Render.com free tier

# ============ Stage 1: Build frontend assets ============
FROM node:20-alpine AS assets

WORKDIR /app

# Copy package files first (better caching)
COPY package.json package-lock.json* ./

# Install ALL deps (vite + laravel-vite-plugin live in devDependencies, needed for build)
RUN npm install --no-audit --no-fund

# Copy the rest and build (non-fatal: app still boots even if assets fail)
COPY . .
RUN npm run build 2>&1 || echo "WARN: vite build failed, continuing"

# ============ Stage 2: PHP runtime ============
FROM php:8.2-fpm-alpine AS runtime

# Install system deps + PHP extensions
RUN apk add --no-cache \
    nginx \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    libxml2-dev \
    oniguruma-dev \
    mysql-client \
    bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli zip gd mbstring exif pcntl bcmath opcache xml

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Composer files + install
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader

# Copy app code
COPY . .

# Copy built assets from stage 1
COPY --from=assets /app/public/build ./public/build

# Generate optimized autoloader
RUN composer dump-autoload --no-dev --optimize --no-interaction

# Storage + bootstrap permissions
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# PHP production config
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time = 120" >> /usr/local/etc/php/conf.d/custom.ini

EXPOSE 8000

# Entrypoint + CMD as root so it can chown if needed; PHP-FPM runs as www-data
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
