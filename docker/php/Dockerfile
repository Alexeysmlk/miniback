FROM php:8.2-fpm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpq-dev \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install pdo pdo_pgsql zip

RUN groupadd -g ${GID} --system laravel
RUN useradd -u ${UID} --system -g laravel -G www-data laravel

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html

USER laravel