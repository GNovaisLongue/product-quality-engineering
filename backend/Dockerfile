# syntax=docker/dockerfile:1

# --- shared composer stage -------------------------------------------------
FROM composer:2.8 AS composer_base
WORKDIR /app/backend
COPY composer.json composer.lock /app/backend/

# --- dependencies WITH dev packages (phpunit) — used for the test image ---
FROM composer_base AS deps_dev
RUN composer install --no-interaction --prefer-dist

# --- dependencies WITHOUT dev packages — used for the production image ----
FROM composer_base AS deps_prod
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# --- test image: runs `phpunit` against the domain unit suite -------------
# Build:  docker compose -f compose.yml -f compose.test.yml build backend-test
# Run:    docker compose -f compose.yml -f compose.test.yml run --rm backend-test
FROM php:8.2-cli AS test
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo pdo_mysql
WORKDIR /app/backend
COPY --from=deps_dev /app/backend/vendor ./vendor
COPY . .
CMD ["vendor/bin/phpunit", "-c", "phpunit.xml", "--testsuite", "unit"]

# --- production image: apache + php, no dev deps, no test files -----------
FROM php:8.2-apache AS production
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo pdo_mysql

WORKDIR /var/www/html

COPY --from=deps_prod /app/backend/vendor /var/www/html/vendor
COPY ./index.php /var/www/html/index.php
COPY ./App /var/www/html/App
COPY ./apache-product.conf /etc/apache2/conf-enabled/product.conf

CMD ["apache2-foreground"]
