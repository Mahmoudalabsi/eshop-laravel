# Multi-stage Dockerfile for Laravel 12 ecommerce-eshop (Frontend)
# Optimized for Render.com / Railway / Fly.io / any container platform

FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    sqlite-libs \
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
    git \
    curl \
    bash

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        zip \
        gd \
        mbstring \
        exif \
        pcntl \
        bcmath \
        opcache \
        xml

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (no dev in production)
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader

# Copy application code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --no-dev --optimize --no-interaction

# Install Node.js and build frontend assets
RUN apk add --no-cache nodejs npm \
    && npm install --no-audit --no-fund \
    && npm run build \
    && apk del nodejs npm

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Configure PHP for production
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time = 120" >> /usr/local/etc/php/conf.d/custom.ini

EXPOSE 8000

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

USER www-data

ENTRYPOINT ["docker-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
