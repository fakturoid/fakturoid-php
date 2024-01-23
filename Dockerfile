ARG PHP_VERSION=8.1
FROM composer:2 AS composer

FROM php:${PHP_VERSION}

RUN apt-get update \
     && apt-get install -y libzip-dev zlib1g-dev zlib1g-dev zip git libfcgi-bin jq libpng-dev libonig-dev unzip \
     && apt-get clean \
     && rm -rf /var/lib/apt/list/* \
     && pecl install xdebug \
     && docker-php-ext-enable xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer
