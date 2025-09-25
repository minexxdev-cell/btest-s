FROM php:8.1-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    wget \
    netcat-openbsd \
    oniguruma-dev \
    libxml2-dev \
    curl \
    nodejs \
    npm

# Create necessary directories
RUN mkdir -p /run/nginx /app /var/log/nginx

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy application code
COPY . /app

# Install Composer
RUN wget https://getcomposer.org/installer -O - | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring

# Install application dependencies
RUN cd /app && \
    /usr/local/bin/composer install --no-dev --optimize-autoloader

# Install npm dependencies and build assets (if using Laravel Mix)
RUN cd /app && \
    if [ -f "package.json" ]; then \
        npm install --production && \
        npm run production; \
    fi

# Create symlink for storage (if needed)
RUN cd /app && \
    php artisan storage:link || true

# Set proper permissions
RUN chown -R www-data:www-data /app
RUN chmod -R 755 /app/storage /app/bootstrap/cache
RUN chmod -R 755 /app/public

# Ensure AdminLTE assets exist and have proper permissions
RUN if [ -d "/app/public/AdminLTE-2" ]; then \
        chmod -R 755 /app/public/AdminLTE-2; \
        chown -R www-data:www-data /app/public/AdminLTE-2; \
    fi

# Ensure all public assets have proper permissions
RUN find /app/public -type f -exec chmod 644 {} \;
RUN find /app/public -type d -exec chmod 755 {} \;

# Make startup script executable
RUN chmod +x /app/docker/startup.sh

# Expose port (for documentation, actual port is set by Cloud Run)
EXPOSE 8080

CMD ["sh", "/app/docker/startup.sh"]