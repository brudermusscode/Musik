FROM php:8.5-fpm

RUN DEBIAN_FRONTEND=noninteractive apt update -y \
    && apt install -y \
    libicu-dev \
    libcurl4-openssl-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libgif-dev \
    libavif-dev \
    libwebp-dev

# Set the production php.ini to default
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Remove unused files from php directory
RUN rm -rf -r /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini-production

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-avif \
    --with-webp \
    && docker-php-ext-install gd
RUN docker-php-ext-install pdo_mysql intl exif curl zip
RUN docker-php-ext-enable pdo_mysql intl exif curl zip gd

# Install custom config files.
COPY zz-custom.ini /usr/local/etc/php/conf.d/zz-custom.ini
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

EXPOSE 9000

CMD ["php-fpm", "-F"]