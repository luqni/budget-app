#!/bin/sh

# Exit on error
set -e

# Wait for database if needed (optional)
# sleep 5

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run seeders
echo "Running seeders..."
php artisan db:seed --force

# Cache configuration and routes
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage
echo "Linking storage..."
php artisan storage:link || true

# Fix permissions again just in case (for runtime files)
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/database

# Start Supervisor (which starts Nginx and PHP-FPM)
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf