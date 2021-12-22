MAINTAINER Cvar1984 <cvar1984@protonmail.com>

FROM php:7.4-cli

RUN apt-get update &% apt-get upgrade -y 

RUN pecl install redis-5.1.1 \
    && pecl install xdebug-2.8.1 \
    && docker-php-ext-enable redis xdebug

RUN curl -sS https://getcomposer.org/installer |\
php -- --install-dir=/usr/local/bin \
--filename=composer

RUN composer install

CMD ["php", "./my_bot.php"]
