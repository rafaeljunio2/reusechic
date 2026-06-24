FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Instala dependências PHP (camada cacheada — só reinstala se composer.json mudar)
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-autoloader --no-scripts

COPY . /var/www/html/

RUN composer dump-autoload --no-dev --optimize

RUN chown -R www-data:www-data /var/www/html \
    && chmod 775 /var/www/html/uploads

EXPOSE 80
