FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install mysqli pdo_mysql mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY . .

CMD ["apache2-foreground"]