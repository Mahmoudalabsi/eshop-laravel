# Dockerfile for ecommerce-shop (Backend - Laravel API + Admin)
# Multi-stage build: Node 20 for assets, PHP 8.2-fpm-alpine + nginx for runtime
# Designed for Fly.io (listen on PORT env var, default 8080)

# ============ Stage 1: Build frontend assets ============
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm install --no-audit --no-fund

COPY . .
RUN npm run build 2>&1 || echo "WARN: vite build failed, continuing"

# ============ Stage 2: PHP runtime with nginx ============
FROM php:8.2-fpm-alpine AS runtime

# Install nginx + system deps + PHP extensions
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
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli zip gd mbstring exif pcntl bcmath opcache xml

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Composer files + install (no scripts yet, autoload later)
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader

# Copy app code
COPY . .

# Copy built assets from stage 1
COPY --from=assets /app/public/build ./public/build

# Generate optimized autoloader
RUN composer dump-autoload --no-dev --optimize --no-interaction

# Storage + bootstrap permissions
RUN mkdir -p storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             storage/app/public \
             bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# PHP production config
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time = 120" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.enable = 1" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.memory_consumption = 128" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.max_accelerated_files = 10000" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.validate_timestamps = 0" >> /usr/local/etc/php/conf.d/custom.ini

# Nginx config: listen on PORT env var (Fly.io requirement)
RUN mkdir -p /run/nginx
RUN cat > /etc/nginx/http.d/default.conf <<'NGINX'
server {
    listen 8080 default_server;
    listen [::]:8080 default_server;
    server_name _;
    root /var/www/html/public;
    index index.php index.html;

    client_max_body_size 64M;

    # Logs
    access_log /dev/stdout;
    error_log  /dev/stderr;

    # Storage symlink already created at build time
    location /storage/ {
        alias /var/www/html/storage/app/public/;
    }

    # Static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|map)$ {
        try_files $uri =404;
        expires 1d;
        add_header Cache-Control "public, immutable";
    }

    # Main Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_read_timeout 120;
    }

    location = /favicon.ico { return 204; access_log off; log_not_found off; }
    location = /robots.txt  { return 204; access_log off; log_not_found off; }
}
NGINX

# Supervisor config: run both nginx + php-fpm
RUN cat > /etc/supervisord.conf <<'SUP'
[supervisord]
nodaemon=true
user=root
logfile=/dev/null
logfile_maxbytes=0
pidfile=/var/run/supervisord.pid

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:php-fpm]
command=php-fpm -F
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
SUP

EXPOSE 8080

# Entrypoint + CMD
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
