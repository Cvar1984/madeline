FROM debian:jessie

MAINTAINER Cvar1984 <cvar1984@protonmail.com>

# Install packages
RUN apt-get update && apt-get install -y \
php7.3 php7.3-mbstring php7.3-sqlite composer

RUN composer install

CMD ["php7.3", "./my_bot.php"]
