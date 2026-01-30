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

# Handle npm dependencies and asset compilation
RUN cd /app && \
    if [ -f "package.json" ]; then \
        echo "package.json found, installing npm dependencies..."; \
        npm install; \
        if npm run --silent production > /dev/null 2>&1; then \
            echo "Running npm run production..."; \
            npm run production; \
            echo "Pruning dev dependencies..."; \
            npm prune --production; \
        else \
            echo "No production script found or Laravel Mix not available, skipping asset compilation"; \
        fi; \
    else \
        echo "No package.json found, skipping npm install"; \
    fi

# Create symlink for storage (if needed)
RUN cd /app && \
    php artisan storage:link || true

# Create logs directory with proper permissions
RUN mkdir -p /app/storage/logs && \
    chown -R www-data:www-data /app/storage/logs && \
    chmod -R 775 /app/storage/logs

# Set proper permissions
RUN chown -R www-data:www-data /app
RUN chmod -R 775 /app/storage /app/bootstrap/cache
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