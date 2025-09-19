FROM php:8.1-fpm-alpine

RUN apk add --no-cache nginx wget

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /app
COPY . /app

# Fix permissions BEFORE composer install
RUN chmod -R 775 /app/storage /app/bootstrap/cache
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

# Laravel setup
RUN cd /app && if [ ! -f .env ]; then cp .env.example .env; fi
RUN cd /app && php artisan key:generate
RUN cd /app && php artisan config:clear
RUN cd /app && php artisan cache:clear

RUN chown -R www-data: /app
RUN chmod +x /app/docker/startup.sh

CMD sh /app/docker/startup.sh
