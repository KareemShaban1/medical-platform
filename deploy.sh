#!/bin/bash

# Laravel Deployment Script for Contabo Server
# This script can be run manually on the server or called by CI/CD

set -e

echo "🚀 Starting deployment..."

# Navigate to project directory
cd "$(dirname "$0")"

# Pull latest changes
echo "📥 Pulling latest changes..."
git fetch origin
git reset --hard origin/master || git reset --hard origin/main

# Install/update Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install/update NPM dependencies
echo "📦 Installing NPM dependencies..."
npm ci --production

# Build assets
echo "🔨 Building assets..."
npm run build

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear and cache configuration
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear application cache
php artisan cache:clear

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || chown -R $USER:$USER storage bootstrap/cache || true

# Restart queue workers (if using supervisor)
echo "🔄 Restarting queue workers..."
php artisan queue:restart || true

# Reload PHP-FPM (adjust based on your PHP version)
echo "🔄 Reloading PHP-FPM..."
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
sudo systemctl reload php${PHP_VERSION}-fpm || sudo systemctl reload php-fpm || true

echo "✅ Deployment completed successfully!"













