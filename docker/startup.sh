#!/bin/sh
set -e

cd /var/www/html

# Railway sets PORT dynamically; default to 8080
export PORT="${PORT:-8080}"

# Generate nginx config with the correct PORT
envsubst '${PORT}' < /etc/nginx/sites-available/default.conf.template \
    > /etc/nginx/sites-available/default

# Enable the site
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Cache configuration in production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching config, routes and views..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Ensure correct storage permissions
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start nginx + php-fpm via supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
