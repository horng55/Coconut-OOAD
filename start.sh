#!/bin/bash

# Exit on error
set -e

echo "🚀 Starting Khmershop Online Application Setup..."

# Check if .env file exists, if not create it
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
fi

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install NPM dependencies and build assets
echo "📦 Installing NPM dependencies..."
npm install
npm run build

# Generate application key if not exists
if ! grep -q "APP_KEY=base64" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

# Set proper permissions
echo "🔒 Setting proper permissions..."
chown -R www-data:www-data /var/www
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Create necessary directories
echo "📁 Creating necessary directories..."
mkdir -p /var/www/storage/framework/{sessions,views,cache}
chown -R www-data:www-data /var/www/storage/framework
chmod -R 775 /var/www/storage/framework

# Clear and cache configuration
echo "🔄 Clearing and caching configuration..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Start services
echo "🚀 Starting services..."

# Start PHP-FPM
echo "🔄 Starting PHP-FPM..."
php-fpm

# Start Nginx
echo "🔄 Starting Nginx..."
nginx -g "daemon off;"

echo "✅ Setup completed successfully!" 