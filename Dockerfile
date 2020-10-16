# First step: build container
# docker build -t google-street-view .
# Second step: install dependencies with composer
# docker run -it --rm --name google-street-view -v "$PWD":/application -w /application google-street-view php /usr/bin/composer install
# Last step: launch example
# docker run -it --rm --name google-street-view -v "$PWD":/application -w /application google-street-view php example.php
# Unit test
# docker run -it --rm --name google-street-view -v "$PWD":/application -w /application google-street-view vendor/bin/phpunit

FROM php:7.4-fpm

# Install zip
RUN apt-get update && apt-get install -y zip

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN mkdir /var/composer
ENV COMPOSER_HOME /var/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install xDebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Acces to source code
COPY . /application
WORKDIR /application

CMD [ "php-fpm" ]
