# syntax=docker/dockerfile:1
# Prebuilt PHP 8.3 image (all extensions baked in, no compilation needed)
FROM thecodingmachine/php:8.3-v4-fpm

# Enable the PHP extensions the app needs (the .so files ship with the image)
ENV PHP_EXTENSION_GD=1 \
    PHP_EXTENSION_BCMATH=1 \
    PHP_EXTENSION_PDO_MYSQL=1 \
    PHP_EXTENSION_INTL=1 \
    PHP_EXTENSION_ZIP=1 \
    PHP_EXTENSION_REDIS=1 \
    PHP_EXTENSION_EXIF=1 \
    PHP_EXTENSION_PCNTL=1 \
    PHP_EXTENSION_OPCACHE=1

WORKDIR /var/www/html

# Install PHP dependencies first for better layer caching
COPY --chown=docker:docker composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copy application source and finish autoloading
COPY --chown=docker:docker . .
RUN composer dump-autoload --optimize --no-dev

COPY --chown=docker:docker docker/entrypoint.sh /usr/local/bin/app-entrypoint.sh
RUN chmod +x /usr/local/bin/app-entrypoint.sh

# The base image's ENTRYPOINT enables extensions, then runs this CMD.
# app-entrypoint.sh runs setup, then exec's the process passed as its args.
CMD ["app-entrypoint.sh", "php-fpm"]
