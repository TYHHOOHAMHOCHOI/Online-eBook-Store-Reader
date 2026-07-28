FROM php:8.3-fpm-alpine

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html

COPY . .

RUN mkdir -p storage/logs \
    && chown -R www-data:www-data storage

EXPOSE 9000

CMD ["php-fpm"]

