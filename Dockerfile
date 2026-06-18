FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . .

RUN rm -rf .git \
    && mkdir -p uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod 775 uploads

EXPOSE 80

CMD ["apache2-foreground"]
