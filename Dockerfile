FROM php:8.2-apache

# Install PDO MySQL extension and mysql client (for healthcheck/init)
RUN apt-get update && apt-get install -y default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite and set AllowOverride All
RUN a2enmod rewrite \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . .

# uploads must be writable by Apache; .git is not needed in the image
RUN rm -rf .git \
    && mkdir -p uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod 775 uploads

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
