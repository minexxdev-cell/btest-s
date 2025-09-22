FROM php:8.1-fpm-alpine

RUN apk add --no-cache nginx wget oniguruma-dev

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /app
COPY . /app

RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

# Install build dependencies and required packages
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring tokenizer \
    && apk del .build-deps

RUN chown -R www-data: /app

CMD sh /app/docker/startup.sh