FROM php:7.3.3-apache-stretch as base

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
 && a2enmod \
      rewrite \
      headers \
      actions

RUN apt-get update && apt-get install -y \
      libgmp-dev \
      libpq-dev \
      libzip-dev \
      zlib1g-dev \
  && docker-php-ext-install -j$(nproc) \
      bcmath \
      gmp \
      pgsql \
      zip \
  && rm -rf /var/lib/apt/lists/*

# install vendor packages
FROM composer:1.8.4 as backend_dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader

# build the frontend - currently unused?
# FROM node:8 as frontend_builder
# WORKDIR /home/node/app

# COPY package.json yarn.lock ./
# RUN yarn install --frozen-lockfile

# COPY webpack.mix.js .
# COPY resources/assets resources/assets
# RUN yarn run production

FROM base
WORKDIR /var/www/html

COPY --chown=www-data . .
COPY --chown=www-data --from=backend_dependencies /app/vendor vendor

USER www-data

# generate autoloader
COPY --chown=www-data --from=backend_dependencies /usr/bin/composer /tmp/composer
RUN COMPOSER_CACHE_DIR=/dev/null /tmp/composer install --no-dev --no-interaction --no-scripts --classmap-authoritative \
 && rm -rf /tmp/composer

VOLUME ['/var/www/html/storage']
USER root
EXPOSE 80
