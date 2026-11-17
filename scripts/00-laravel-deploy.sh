#!/usr/bin/env bash

echo "Installing Composer dependencies..."
composer install --no-dev --working-dir=/var/www/html

echo "Clearing old caches..."
php artisan optimize:clear

echo "Creating storage link..."
php artisan storage:link || true

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Caching views..."
php artisan view:cache

echo "Running migrations..."
php artisan migrate --force || true

echo "Running database seeders..."
php artisan db:seed --force || true

echo "Deployment script finished successfully!"