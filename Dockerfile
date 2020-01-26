FROM php:7.4-cli-buster

RUN apt-get update \
    && apt-get install -y vim git zlib1g-dev libzip-dev libmpdec-dev \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo 'xdebug.remote_enable=on' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo 'xdebug.remote_host=host.docker.internal' >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo 'xdebug.remote_port=9000' >>  /usr/local/etc/php/conf.d/xdebug.ini \
    && docker-php-ext-enable xdebug \
    && pecl install decimal \
    && docker-php-ext-enable decimal \
    && curl -sS https://getcomposer.org/installer \
     | php -- --install-dir=/usr/local/bin --filename=composer \
