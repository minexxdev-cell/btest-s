#!/bin/sh

echo "Starting Laravel application..."

# Replace the port placeholder in nginx config
sed -i "s,LISTEN_PORT,$PORT,g" /etc/nginx/nginx.conf

# Set proper permissions for all files
echo "Setting permissions..."
chown -R www-data:www-data /app
chmod -R 775 /app/storage /app/bootstrap/cache
chmod -R 755 /app/public

# Ensure AdminLTE and other assets have proper permissions
if [ -d "/app/public/AdminLTE-2" ]; then
    echo "Setting AdminLTE permissions..."
    chmod -R 755 /app/public/AdminLTE-2
    chown -R www-data:www-data /app/public/AdminLTE-2
fi

# Clear and optimize Laravel caches for production
echo "Optimizing Laravel..."
cd /app
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D

# Wait for PHP-FPM to start
echo "Waiting for PHP-FPM to start..."
while ! nc -w 1 -z 127.0.0.1 9000; do 
    echo "Waiting for PHP-FPM..."
    sleep 1
done
echo "PHP-FPM is ready!"

# Test nginx configuration
echo "Testing nginx configuration..."
nginx -t

# Start Nginx in foreground
echo "Starting nginx..."
exec nginx