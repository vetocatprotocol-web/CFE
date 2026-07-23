#!/bin/bash

# Railway Post-Deploy Script
# This script runs after each deployment

set -e

echo "🚀 Starting deployment..."

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "📝 Generating application key..."
    php artisan key:generate --force
fi

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed database if first deploy (check if users table is empty)
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
fi

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage
echo "🔗 Linking storage..."
php artisan storage:link --force

# Clear old caches
echo "🧹 Clearing old caches..."
php artisan cache:clear

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "✅ Deployment complete!"
echo ""
echo "📊 Application Status:"
echo "   - URL: $APP_URL"
echo "   - Environment: $APP_ENV"
echo "   - Debug: $APP_DEBUG"
