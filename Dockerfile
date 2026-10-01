FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY assets ./assets
COPY partials ./partials
COPY resources ./resources
COPY bin/assets.js ./bin/assets.js
RUN npm run build
FROM php:8.3-apache
RUN apt-get update && apt-get install -y libpq-dev libonig-dev libxml2-dev unzip git && docker-php-ext-install pdo_pgsql mbstring dom xml xmlwriter && a2enmod rewrite && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-interaction --prefer-dist && mkdir -p storage/uploads storage/logs && chown -R www-data:www-data storage
COPY --from=assets /app/public/assets ./public/assets
COPY config/apache.conf /etc/apache2/sites-available/000-default.conf
COPY config/php.ini /usr/local/etc/php/conf.d/dolce.ini
