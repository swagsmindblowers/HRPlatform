# --- Stage 1: build frontend assets ---
FROM node:22 AS assets
WORKDIR /app
COPY package.json yarn.lock ./
RUN CYPRESS_INSTALL_BINARY=0 yarn install --frozen-lockfile
COPY . .
RUN yarn production

# --- Stage 2: PHP dependencies ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# --- Stage 3: runtime image ---
FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" intl pdo_mysql mbstring zip gd bcmath \
    && apt-get purge -y --auto-remove libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/js ./public/js
COPY --from=assets /app/public/css ./public/css
COPY --from=assets /app/public/mix-manifest.json ./public/mix-manifest.json

RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && php artisan lang:generate \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
CMD php artisan setup --force -vvv \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
