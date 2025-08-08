FROM composer as composer
FROM php:8

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libxml2-dev \
    libxslt1-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) xsl

COPY . /var/www/html

RUN ls -lh /var/www/html

WORKDIR /var/www/html

RUN composer update --no-dev --optimize-autoloader