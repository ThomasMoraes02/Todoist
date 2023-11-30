FROM php:8.2-apache

RUN apt update && \
    apt install -y libpq-dev && \
    docker-php-ext-install pdo_mysql

COPY . /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]