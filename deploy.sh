#!/bin/bash

# ============================================
# Railway Post-Deploy Script
# ============================================

set -e

echo "🚀 Starting deployment..."

# Check if we're in production
if [ "$APP_ENV" = "production" ]; then
    echo "📦 Production environment detected"
fi

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "📝 Generating application key..."
    php artisan key:generate --force
fi

# Create required directories
echo "📁 Creating directories..."
mkdir -p \
    storage/logs \
    storage/framework/cache/data \
    storage/framework/cache/streams \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    bootstrap/cache

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed database if first deploy (check if users table is empty)
echo "🔍 Checking if database needs seeding..."
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
fi

# Clear and cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Link storage
echo "🔗 Linking storage..."
php artisan storage:link --force

# Optimize autoloader
echo "📚 Optimizing autoloader..."
composer dump-autoload --optimize --no-dev

echo ""
echo "✅ Deployment complete!"
echo ""
echo "📊 Application Status:"
echo "   - URL: ${APP_URL:-http://localhost}"
echo "   - Environment: ${APP_ENV:-local}"
echo "   - Debug: ${APP_DEBUG:-true}"
