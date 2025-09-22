#!/bin/sh

# Set the port from environment variable (default to 8080)
PORT=${PORT:-8080}

echo "Starting Laravel application on port $PORT"

# Ensure correct permissions
chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Update nginx configuration with the correct port
sed -i "s/8080/$PORT/g" /etc/nginx/nginx.conf

# Start supervisord to manage both PHP-FPM and Nginx
exec /usr/bin/supervisord -c /etc/supervisord.conf