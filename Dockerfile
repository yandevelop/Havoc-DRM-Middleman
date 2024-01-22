FROM php:8.2-apache
RUN apt-get update && apt-get upgrade -y

RUN apt-get -y update \
&& apt-get install -y libicu-dev \
&& apt-get install -y git \
&& apt-get install -y zip \
&& apt-get install -y curl \
&& apt-get install -y unzip \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl

WORKDIR /var/www/html/
RUN a2enmod rewrite
ADD . /var/www/html

RUN chown -R www-data:www-data /var/www

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

EXPOSE 80/tcp
EXPOSE 443/tcp