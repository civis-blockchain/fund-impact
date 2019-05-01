FROM node:8 as builder

WORKDIR /fund-impact
COPY ./Script ./
RUN npm install

FROM php:7.3-fpm

RUN apt-get update -qq &&  apt-get install -y libpng-dev git zip unzip

RUN docker-php-ext-install pdo pdo_mysql mysqli gd

#Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

WORKDIR /fund-impact

COPY --from=builder /fund-impact ./
RUN composer install

EXPOSE 80