FROM php:7.4-apache

COPY my-apache.conf /etc/apache2/sites-available/000-default.conf

RUN pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install pdo_mysql

COPY xdebug.ini /usr/local/etc/php/conf.d/

RUN a2enmod rewrite
