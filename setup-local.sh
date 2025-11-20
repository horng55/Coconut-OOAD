#!/bin/bash

# Local Development Setup Script
# This script sets up the environment for local development (IDE) with Docker only for DB/Redis

set -e

echo "🚀 Setting up School Management System for Local Development"
echo "============================================================"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.dev..."
    if [ -f .env.dev ]; then
        cp .env.dev .env
        echo "✅ .env file created from .env.dev"
    else
        echo "❌ .env.dev file not found. Please create .env file manually"
        exit 1
    fi
else
    echo "✅ .env file already exists"
fi

# Update .env for local development
echo ""
echo "🔧 Updating .env for local development..."
# Update database settings for MySQL
sed -i.bak 's/DB_CONNECTION=pgsql/DB_CONNECTION=mysql/' .env 2>/dev/null || sed -i '' 's/DB_CONNECTION=pgsql/DB_CONNECTION=mysql/' .env
sed -i.bak 's/DB_HOST=db/DB_HOST=127.0.0.1/' .env 2>/dev/null || sed -i '' 's/DB_HOST=db/DB_HOST=127.0.0.1/' .env
sed -i.bak 's/DB_HOST=$/DB_HOST=127.0.0.1/' .env 2>/dev/null || sed -i '' 's/DB_HOST=$/DB_HOST=127.0.0.1/' .env
sed -i.bak 's/DB_PORT=5432/DB_PORT=3306/' .env 2>/dev/null || sed -i '' 's/DB_PORT=5432/DB_PORT=3306/' .env
sed -i.bak 's/DB_DATABASE=$/DB_DATABASE=school_management/' .env 2>/dev/null || sed -i '' 's/DB_DATABASE=$/DB_DATABASE=school_management/' .env
sed -i.bak 's/DB_USERNAME=$/DB_USERNAME=root/' .env 2>/dev/null || sed -i '' 's/DB_USERNAME=$/DB_USERNAME=root/' .env
sed -i.bak 's/DB_PASSWORD=$/DB_PASSWORD=root/' .env 2>/dev/null || sed -i '' 's/DB_PASSWORD=$/DB_PASSWORD=root/' .env
# Update Redis settings
sed -i.bak 's/REDIS_HOST=redis/REDIS_HOST=127.0.0.1/' .env 2>/dev/null || sed -i '' 's/REDIS_HOST=redis/REDIS_HOST=127.0.0.1/' .env
# Update app settings
sed -i.bak 's/APP_NAME=KhmerShop/APP_NAME="School Management System"/' .env 2>/dev/null || sed -i '' 's/APP_NAME=KhmerShop/APP_NAME="School Management System"/' .env
sed -i.bak 's|APP_URL=http://localhost|APP_URL=http://localhost:8000|' .env 2>/dev/null || sed -i '' 's|APP_URL=http://localhost|APP_URL=http://localhost:8000|' .env
rm -f .env.bak
echo "✅ .env updated for local development (MySQL + Redis)"

# Start Docker services (DB & Redis only)
echo ""
echo "🐳 Starting Docker services (Database & Redis)..."
docker-compose up -d

# Wait for database to be ready
echo ""
echo "⏳ Waiting for database to be ready..."
sleep 10

# Install Composer dependencies
echo ""
echo "📦 Installing Composer dependencies..."
composer install --no-interaction

# Install NPM dependencies
echo ""
echo "📦 Installing NPM dependencies..."
npm install

# Generate application key if not exists
if ! grep -q "APP_KEY=base64" .env 2>/dev/null; then
    echo ""
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Set permissions
echo ""
echo "🔒 Setting permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Run migrations
echo ""
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database
echo ""
echo "🌱 Seeding database..."
php artisan db:seed --force || echo "⚠️  Seeding skipped (may already be seeded)"

echo ""
echo "✅ Setup complete!"
echo ""
echo "📋 Next steps:"
echo "   1. Start frontend dev server: npm run dev"
echo "   2. Start Laravel server: php artisan serve"
echo "   3. Access: http://localhost:8000"
echo ""
echo "🔐 Login credentials:"
echo "   Email: admin@schoolme.com"
echo "   Password: password123"
echo ""

