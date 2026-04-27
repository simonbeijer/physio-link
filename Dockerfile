FROM php:8.3-apache

# System libs for PHP extensions + composer
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        libpq-dev \
        libzip-dev \
        libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        bcmath \
        zip \
        intl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Production opcache settings
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Apache: enable rewrite, point DocumentRoot at Laravel public/
RUN a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!<Directory /var/www/>!<Directory /var/www/html/public/>!g' /etc/apache2/apache2.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Cache composer layer: install deps before copying full source
COPY src/composer.json src/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copy app
COPY src/ ./

# Finalize autoload now that source is present
RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Startup script: cache config, run migrations, then hand off to Apache.
# NOTE: migrate --force on every container start is fine (idempotent) for
# now. Move to a Render Pre-Deploy hook once the service is stable so the
# web container is not coupled to migration success on every boot.
RUN { \
        echo '#!/usr/bin/env bash'; \
        echo 'set -e'; \
        echo ': "${PORT:=10000}"'; \
        echo 'sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf'; \
        echo 'sed -ri "s/:80>/:${PORT}>/" /etc/apache2/sites-enabled/000-default.conf'; \
        echo 'php artisan config:cache'; \
        echo 'php artisan migrate --force'; \
        echo 'php artisan db:seed --force'; \
        echo 'exec apache2-foreground'; \
    } > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]
