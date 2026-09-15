# composer
FROM composer:2.8 AS backend_builder

WORKDIR /app/backend/
COPY composer.json composer.lock /app/backend/
RUN composer install --no-dev --optimize-autoloader

# apache
FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo pdo_mysql

WORKDIR /var/www/html

COPY --from=backend_builder /app/backend/vendor /var/www/html/vendor
COPY ./index.php /var/www/html/index.php
COPY ./App /var/www/html/App
COPY ./apache-product.conf /etc/apache2/conf-enabled/product.conf

CMD ["apache2-foreground"]
