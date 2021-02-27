# First step: build container
# docker build -t google-street-view .
#
# Last step: launch example
# docker run -it --rm --name google-street-view -v "$(pwd)/example":/application/example -v "$(pwd)/src":/application/src -p 8080:80 google-street-view
# Open your browser and go to http://localhost:8080/
#
# Bonus: launch example wth CLI interpreter
# docker run -it --rm --name google-street-view -v "$(pwd)/example":/application/example -v "$(pwd)/src":/application/src google-street-view php example/index.php
#
# Unit test
# docker run -it --rm --name google-street-view -v "$(pwd)/src":/application/src -v "$PWD/tests":/application/tests google-street-view vendor/bin/phpunit

FROM php:7.2-apache

MAINTAINER Joël Gaujard <j.gaujard@gmail.com>

# Install zip
RUN apt-get update && apt-get install -y zip

# Install xDebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Change Apache document root
ENV APACHE_DOCUMENT_ROOT /application/example
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Access to source code
COPY . /application
WORKDIR /application

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN mkdir /var/composer
ENV COMPOSER_HOME /var/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN /usr/bin/composer install --prefer-dist

EXPOSE 80
