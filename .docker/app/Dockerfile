FROM php:8.4.5-fpm-bookworm AS base

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip vim \
       libmemcached-dev libz-dev libicu-dev libzip-dev libc-client-dev libkrb5-dev librabbitmq-dev libxml2-dev \
    && docker-php-ext-install opcache pdo_mysql intl zip \
    && apt-get install git -y \
    && docker-php-source delete \
    && apt-get remove -y libmemcached-dev libz-dev libicu-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*


# Composer
FROM composer:2 as composer

# Dev
FROM base as dev

RUN usermod -u 1000 www-data --shell /bin/bash && groupmod -g 1000 www-data


ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends poppler-utils \
    && pecl -q install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-source delete \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app
VOLUME /var

RUN chown -R www-data:www-data /app

RUN mkdir /composer && chown www-data:www-data /composer
COPY ./.docker/app/zz-dev.ini /usr/local/etc/php/conf.d/zz-dev.ini

ENV COMPOSER_HOME /composer

USER www-data:www-data