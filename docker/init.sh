#!/bin/bash

# Quick initialization script for Docker setup

set -e

echo "🚀 School Management System - Docker Setup"
echo "=========================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "✅ .env file created. Please review and update if needed."
else
    echo "✅ .env file already exists."
fi

# Start Docker containers
echo ""
echo "🐳 Starting Docker containers..."
docker-compose up -d

# Wait for database to be ready
echo ""
echo "⏳ Waiting for database to be ready..."
sleep 10

# Install Composer dependencies
echo ""
echo "📦 Installing Composer dependencies..."
docker-compose exec -T app composer install --no-interaction

# Install NPM dependencies
echo ""
echo "📦 Installing NPM dependencies..."
docker-compose exec -T app npm install

# Generate application key
echo ""
echo "🔑 Generating application key..."
docker-compose exec -T app php artisan key:generate --force

# Set permissions
echo ""
echo "🔒 Setting permissions..."
docker-compose exec -T app chmod -R 775 storage bootstrap/cache || true

# Run migrations
echo ""
echo "🗄️ Running database migrations..."
docker-compose exec -T app php artisan migrate --force

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Access the application at: http://localhost:8000"
echo "🗄️ Access PHPMyAdmin at: http://localhost:8080"
echo ""
echo "📚 For more commands, see DOCKER.md or run 'make help'"

