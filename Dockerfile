FROM php:8.1-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    wget \
    oniguruma-dev \
    libxml2-dev

# Create necessary directories
RUN mkdir -p /run/nginx /app

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy application code
COPY . /app

# Install Composer
RUN wget https://getcomposer.org/installer -O - | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions (excluding tokenizer which is causing issues)
RUN docker-php-ext-install pdo pdo_mysql mbstring

# Install application dependencies
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

# Set proper permissions
RUN chown -R www-data: /app

CMD sh /app/docker/startup.sh