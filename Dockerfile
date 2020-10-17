# First step: build container
# docker build -t google-street-view .
#
# Second step: install dependencies with composer
# docker run -it --rm --name google-street-view -v "$PWD":/application -w /application google-street-view php /usr/bin/composer install
#
# Last step: launch example
# docker run -it --rm --name google-street-view -v "$PWD":/application -w /application google-street-view php example.php
#
# Unit test
# docker run -it --rm --name google-street-view -v "$PWD":/application -w /application google-street-view vendor/bin/phpunit

FROM php:7.1-apache

MAINTAINER Joël Gaujard <j.gaujard@gmail.com>

# Install zip
RUN apt-get update && apt-get install -y zip

# Install xDebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Change Apache document root
ENV APACHE_DOCUMENT_ROOT /application/example
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Access to source code
COPY . /application
WORKDIR /application

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN mkdir /var/composer
ENV COMPOSER_HOME /var/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_VENDOR_DIR vendor-docker
RUN /usr/bin/composer install
