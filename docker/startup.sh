#!/bin/bash

# Exit on error
set -e

echo "🚀 Starting School Management System Setup..."

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
until php artisan db:monitor > /dev/null 2>&1; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

echo "✅ Database is ready!"

# Install Composer dependencies if vendor doesn't exist
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Install NPM dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "📦 Installing NPM dependencies..."
    npm install
fi

# Generate application key if not exists
if ! grep -q "APP_KEY=base64" .env 2>/dev/null; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Set proper permissions
echo "🔒 Setting proper permissions..."
sudo chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Create necessary directories
echo "📁 Creating necessary directories..."
mkdir -p /var/www/storage/framework/{sessions,views,cache}
mkdir -p /var/www/storage/logs
chmod -R 775 /var/www/storage/framework /var/www/storage/logs || true

# Clear and cache configuration
echo "🔄 Clearing and caching configuration..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force || true

# Seed database (optional - uncomment if needed)
# echo "🌱 Seeding database..."
# php artisan db:seed --force || true

echo "✅ Setup completed successfully!"

# Start PHP-FPM
echo "🔄 Starting PHP-FPM..."
exec php-fpm
