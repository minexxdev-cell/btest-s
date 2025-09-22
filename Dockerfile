FROM php:8.1-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    wget \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    mysql-client \
    supervisor

RUN mkdir -p /run/nginx

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    tokenizer \
    bcmath \
    gd \
    pcntl \
    exif

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create app directory
RUN mkdir -p /app
COPY . /app
WORKDIR /app

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php-fpm.conf /etc/php81/php-fpm.d/www.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Create necessary Laravel directories
RUN mkdir -p storage/logs \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache

# Cache Laravel configuration (optional, but recommended for production)
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]