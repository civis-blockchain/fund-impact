FROM node:8 as builder

WORKDIR /fund-impact
COPY ./Script ./
RUN npm install

FROM php:7.3-fpm

#RUN apt-get update -qq &&  apt-get install -y libpng-dev git zip unzip
RUN apt-get update -qq
RUN apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    nano \
    libgmp-dev \
    libldap2-dev \
    netcat \
    sqlite3 \
    libsqlite3-dev

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-webp-dir=/usr/include/  --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd pdo pdo_mysql pdo_sqlite zip gmp bcmath pcntl ldap sysvmsg exif


#RUN docker-php-ext-install pdo pdo_mysql mysqli gd

#Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

WORKDIR /fund-impact

COPY --from=builder /fund-impact ./
RUN composer install

EXPOSE 80