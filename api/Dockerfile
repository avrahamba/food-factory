FROM composer:2 AS vendor
WORKDIR /api
COPY . .
RUN composer install \
    --ignore-platform-reqs \
    --no-dev

FROM php:8.3-apache
WORKDIR /api

COPY --from=vendor /api/. .

COPY Dockerfiles/date_timezone.ini /usr/local/etc/php/conf.d/date_timezone.ini
COPY Dockerfiles/expose_php_off.ini /usr/local/etc/php/conf.d/expose_php_off.ini
COPY Dockerfiles/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN apt-get update && \
    apt-get upgrade -y

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
RUN chown -R www-data:www-data /api \
    && find /api -type f -exec chmod 644 {} \; \
    && find /api -type d -exec chmod 755 {} \; \
    && chgrp -R www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && a2enmod rewrite

RUN php artisan key:generate --force && php artisan cache:clear && php artisan config:cache