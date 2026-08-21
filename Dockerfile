# Single-stage build. Frontend build needs PHP (resources/js/langs.js
# imports the generated public/js/langs/*.json files, so php artisan
# lang:generate must run before the webpack build), and PHP needs
# vendor/ + the full app present - so PHP, Composer, and Node all need to
# coexist in one stage rather than being split across stages that don't
# have what the others produced.
FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
        nodejs npm \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" intl pdo_mysql mbstring zip gd bcmath \
    && npm install -g yarn \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

RUN CYPRESS_INSTALL_BINARY=0 yarn install --frozen-lockfile \
    && php artisan lang:generate \
    && yarn mix --production \
    && rm -rf node_modules \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
CMD ["sh", "-c", "php artisan setup --force -vvv && exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
