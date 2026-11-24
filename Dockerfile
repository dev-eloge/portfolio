# syntax=docker/dockerfile:1

# Portfolio d'Eloge : une image PHP (Laravel) et une image Nginx.
# Construction et démarrage : docker compose up -d --build

############################
# PHP 8.3 avec les extensions nécessaires à Laravel
############################
FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache icu-libs libzip fcgi su-exec \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev linux-headers \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql bcmath intl zip opcache \
    && apk del .build-deps

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-portfolio.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-portfolio.conf
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

############################
# Dépendances PHP (sans les outils de développement)
############################
FROM base AS vendor

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

COPY . .
RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && rm -f bootstrap/cache/*.php

############################
# Interface (React, Tailwind) compilée avec Vite
############################
FROM node:22-alpine AS assets

# Nom affiché dans les titres d'onglet (lu au moment de la compilation)
ARG VITE_APP_NAME=Eloge
ENV VITE_APP_NAME=${VITE_APP_NAME}

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .
# Tailwind scanne les vues de pagination de Laravel (déclarées dans app.css)
COPY --from=vendor /var/www/html/vendor/laravel/framework/src/Illuminate/Pagination ./vendor/laravel/framework/src/Illuminate/Pagination
RUN npm run build

############################
# Image PHP finale (php-fpm)
############################
FROM base AS app

COPY --from=vendor --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build
COPY docker/entrypoint.sh /usr/local/bin/portfolio-entrypoint

RUN chmod +x /usr/local/bin/portfolio-entrypoint \
    && rm -f /usr/bin/composer \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["portfolio-entrypoint"]
CMD ["php-fpm"]

############################
# Nginx : sert les fichiers compilés et les fichiers envoyés depuis l'admin,
# et transmet le PHP au conteneur « app »
############################
FROM nginx:1.27-alpine AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
# Photos et CV envoyés depuis l'admin : volume « storage » monté en lecture seule
RUN ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage
