# syntax=docker/dockerfile:1
#
# Dev/baseline image for the legacy Symfony 3.2 app.
# PHP 7.2 matches the original cPanel host (ea-php72) so the app behaves
# identically while we reproduce and verify it before the Symfony upgrade.
#
# NOTE: PHP 7.2 and Debian buster are end-of-life; buster packages now live on
# archive.debian.org. The sources rewrite below makes `apt-get` work again.
# This whole image is a throwaway baseline - it is replaced by docker/php/Dockerfile
# targeting PHP 8.3 once Phase 2 (framework upgrade) lands.
FROM php:7.2-fpm-buster

# Make EOL buster repositories reachable again.
RUN set -eux; \
    sed -i \
      -e 's|http://deb.debian.org/debian|http://archive.debian.org/debian|g' \
      -e 's|http://security.debian.org/debian-security|http://archive.debian.org/debian-security|g' \
      -e 's|http://security.debian.org|http://archive.debian.org|g' \
      -e '/buster-updates/d' \
      /etc/apt/sources.list

RUN set -eux; \
    apt-get -o Acquire::Check-Valid-Until=false update; \
    apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        libonig-dev \
        zlib1g-dev; \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/; \
    docker-php-ext-configure zip --with-libzip; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql intl gd zip opcache mbstring; \
    pecl install apcu-5.1.21; \
    docker-php-ext-enable apcu; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# Composer (pinned to the last release supporting PHP 7.2 builds).
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html

# Composer cache speeds up rebuilds without baking deps into the image,
# so the working tree stays the source of truth in dev.
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

EXPOSE 9000
CMD ["php-fpm"]
