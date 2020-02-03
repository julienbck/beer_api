FROM php:7.3-apache

# Dépendences nécessaires pour les extensions PHP, plus Wget/Git/SSH
RUN apt-get update && apt-get install --no-install-recommends -yq ${BUILD_PACKAGES} \
        build-essential \
        wget \
        ssh \
        git \
        zip \
        unzip \
        libmcrypt-dev \
        libicu-dev \
        libpq-dev \
        libzip-dev \
    && apt-get clean

# Quelques extensions PHP recommmandées
ENV PHP_EXTENSIONS opcache pdo pdo_mysql pcntl intl zip
RUN docker-php-ext-install ${PHP_EXTENSIONS}

# Config PHP
COPY php.ini /usr/local/etc/php/conf.d/50-setting.ini

#php-cs-fixer
RUN wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O /usr/local/bin/php-cs-fixer \
    && chmod +x /usr/local/bin/php-cs-fixer

# Installation de composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin
# Et de prestissimo pour l'accélérer
RUN composer global require hirak/prestissimo

# Activation d'Apache mod_rewrite
RUN a2enmod rewrite

# On configure un vhost, pour ne pas avoir de public/ dans l'URL
COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf