# Используем аргумент сборки для версии PHP
ARG PHP_VERSION=8.4.15-fpm-alpine3.21
FROM php:${PHP_VERSION}

WORKDIR /var/www/html

# Устанавливаем зависимости
RUN apk add --no-cache \
        libzip-dev \
        zip \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libxml2-dev \
        icu-dev \
        gettext

# Устанавливаем расширения PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        opcache

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем и делаем исполняемым наш скрипт точки входа
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

# Устанавливаем entrypoint.sh как точку входа
ENTRYPOINT ["entrypoint.sh"]

# Оставляем CMD для запуска PHP-FPM
CMD ["php-fpm"]