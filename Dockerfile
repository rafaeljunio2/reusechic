FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

COPY reusechic.conf /etc/apache2/conf-available/reusechic.conf
RUN a2enconf reusechic

WORKDIR /var/www/html

COPY . .

RUN rm -rf .git \
    && mkdir -p uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod 775 uploads

EXPOSE 80

CMD ["apache2-foreground"]
