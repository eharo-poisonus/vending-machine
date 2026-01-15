FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install \
      pdo \
      pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .

RUN useradd -u 1000 -m appuser \
    && chown -R appuser:appuser /var/www/html

USER appuser

RUN composer install --optimize-autoloader
