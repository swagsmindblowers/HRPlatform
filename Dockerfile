# --- Stage 1: build frontend assets ---
FROM node:22 AS assets
WORKDIR /app
COPY package.json yarn.lock ./
RUN CYPRESS_INSTALL_BINARY=0 yarn install --frozen-lockfile
COPY . .
RUN yarn production

# --- Stage 2: runtime image ---
FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" intl pdo_mysql mbstring zip gd bcmath \
    && apt-get purge -y --auto-remove libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Full app source, including database/seeds and database/factories that
# composer.json's autoload.classmap needs to be present for --optimize-autoloader.
COPY . .
COPY --from=assets /app/public/js ./public/js
COPY --from=assets /app/public/css ./public/css
COPY --from=assets /app/public/mix-manifest.json ./public/mix-manifest.json

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && php artisan lang:generate \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
CMD ["sh", "-c", "php artisan setup --force -vvv && exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
