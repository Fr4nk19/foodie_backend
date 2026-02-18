#!/bin/sh
set -e

cd /var/www/html

# Railway sets PORT dynamically; default to 8080
export PORT="${PORT:-8080}"

# Generate nginx config with the correct PORT
envsubst '${PORT}' < /etc/nginx/sites-available/default.conf.template \
    > /etc/nginx/sites-available/default
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# ── .env setup ───────────────────────────────────────────────────────────────
# Create .env from .env.example if it doesn't exist (gitignored)
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Generate APP_KEY with PHP directly (avoids artisan needing a valid .env first).
# Then EXPORT it so PHP-FPM workers inherit the value and Laravel never sees an
# empty APP_KEY (the official php:fpm image has clear_env=no by default).
if [ -z "$APP_KEY" ]; then
    APP_KEY=$(php -r 'echo "base64:".base64_encode(random_bytes(32));')
    echo "Generated new APP_KEY"
fi
export APP_KEY

# Write APP_KEY to .env as fallback for any process that doesn't inherit env vars
sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" /var/www/html/.env
# ─────────────────────────────────────────────────────────────────────────────

# Cache configuration for production
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
