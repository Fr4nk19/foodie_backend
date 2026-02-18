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

# ── .env setup ──────────────────────────────────────────────────────────────
# Laravel needs a .env file to write the APP_KEY. Since .env is gitignored,
# we create it from .env.example if it doesn't exist.
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# If APP_KEY is already set as an environment variable, write it to .env
# so artisan commands can use it without re-generating.
if [ -n "$APP_KEY" ]; then
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" /var/www/html/.env
else
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Write other critical env vars to .env so they override .env.example defaults
[ -n "$APP_ENV" ]   && sed -i "s|^APP_ENV=.*|APP_ENV=${APP_ENV}|"     /var/www/html/.env
[ -n "$APP_DEBUG" ] && sed -i "s|^APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}|" /var/www/html/.env
[ -n "$APP_URL" ]   && sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|"     /var/www/html/.env
[ -n "$DB_CONNECTION" ] && sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=${DB_CONNECTION}|" /var/www/html/.env
[ -n "$DB_HOST" ]   && sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST}|"     /var/www/html/.env
[ -n "$DB_PORT" ]   && sed -i "s|^DB_PORT=.*|DB_PORT=${DB_PORT}|"     /var/www/html/.env
[ -n "$DB_DATABASE" ] && sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" /var/www/html/.env
[ -n "$DB_USERNAME" ] && sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" /var/www/html/.env
[ -n "$DB_PASSWORD" ] && sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" /var/www/html/.env
# ────────────────────────────────────────────────────────────────────────────

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
