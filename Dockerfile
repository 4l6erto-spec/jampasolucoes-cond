FROM php:8.3-fpm-alpine

RUN apk add --no-cache git unzip libzip-dev oniguruma-dev postgresql-dev nodejs npm \
    && docker-php-ext-install pdo_pgsql pgsql mbstring zip bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts || true

COPY . .
RUN composer install --prefer-dist --no-interaction --no-scripts \
    && chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]
