#!/usr/bin/env bash
set -e  # titigil agad pag may nag-error

cd /var/www/html

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Clearing old caches..."
php artisan optimize:clear

echo "Creating storage link..."
php artisan storage:link || true

echo "Running migrations..."
php artisan migrate --force

echo "Running database seeders..."
php artisan db:seed --force

echo "Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment script finished successfully!"