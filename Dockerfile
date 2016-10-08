FROM php:7.0-apache

MAINTAINER jdg@combine.dk

# Apache root
RUN rm -Rf /var/www/html ; \
    ln -s /app/public /var/www/html

RUN apt-get update -yqq && apt-get install -y libxml2-dev libicu-dev libfreetype6-dev libmcrypt-dev libjpeg62-turbo-dev libpng12-dev git libcurl4-gnutls-dev libbz2-dev libssl-dev -yqq

# Xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
	&& echo "xdebug.remote_host=10.0.75.1" >> /usr/local/etc/php/conf.d/xdebug.ini

# Set timezone
RUN echo 'date.timezone=UTC' > /usr/local/etc/php/conf.d/date.ini