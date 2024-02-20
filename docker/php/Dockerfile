FROM php:8.3-fpm

RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    unzip \
    libpng-dev \
    libpq-dev \
    && docker-php-ext-install zip pdo pdo_pgsql

WORKDIR /var/www/sunfinance

RUN mkdir var && \
    chmod -R 775 var && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN useradd -u 1000 -m sunfinance

USER sunfinance