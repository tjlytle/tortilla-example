FROM php:8.2.5-fpm

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

WORKDIR /var/www/html