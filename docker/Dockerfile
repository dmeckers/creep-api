# Use the Franken PHP base image
FROM php:8.2-fpm

WORKDIR /var/www/html

# Update package list and install extensions
RUN apt-get update && \
    apt-get install -y \
        curl \
        wget \
        libpq-dev \
        libicu-dev \
        libzip-dev \
        libxml2-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        openssh-client \
        docker.io \
        openssh-server \
        gcc \
        g++ \
        make \
        autoconf \
        libc-dev \
        pkg-config && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo_pgsql \
        intl \
        zip \
        gd \
        xml \
        pcntl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN yes '' |  pecl install redis

RUN docker-php-ext-enable redis

# Copy custom php.ini
COPY ./php.ini /usr/local/etc/php/php.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN composer install && composer dump-autoload

RUN php artisan key:generate && \
    php artisan optimize:clear && \
    php artisan telescope:install && \
    php artisan telescope:publish && \
    php artisan horizon:install && \
    php artisan vendor:publish --tag=pulse-config

ENTRYPOINT [ "php" , "artisan" , "serve" , "--host=0.0.0.0"]
