# syntax=docker/dockerfile:1.6

# PHP-FPM image with required extensions and Composer for Laravel
FROM php:8.3-fpm-alpine

LABEL org.opencontainers.image.source="https://github.com/jadyelsousa/teste-back-end" \
      org.opencontainers.image.title="teste-back-end PHP-FPM" \
      org.opencontainers.image.description="Laravel app runtime for teste-back-end" \
      org.opencontainers.image.licenses="MIT"

# System deps
RUN apk add --no-cache \
    bash \
    git \
    curl \
    icu-dev \
    libzip-dev \
    zlib-dev \
    sqlite-dev \
    oniguruma-dev \
    mysql-client

# PHP extensions commonly used by Laravel
RUN docker-php-ext-install \
    intl \
    pdo_mysql \
    pdo_sqlite \
    bcmath \
    pcntl \
    exif \
    mbstring \
    zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

WORKDIR /var/www/html

# Ensure runtime directories exist
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]
