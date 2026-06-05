#!/bin/bash
# BogorXplore Production Deployment Script
# Run this script to optimize for production

echo "🚀 BogorXplore Production Optimization..."

# Clear and optimize caches
echo "📦 Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Build frontend assets
echo "🎨 Building frontend assets..."
npm run build

# Optimize Composer autoload
echo "📚 Optimizing autoload..."
composer install --optimize-autoloader --no-dev

# Set proper permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Production optimization complete!"
echo ""
echo "Performance tips:"
echo "1. Enable OPcache in php.ini"
echo "2. Use Redis for caching (set CACHE_STORE=redis in .env)"
echo "3. Enable Gzip compression in your web server"
echo "4. Use a CDN for static assets"
echo "5. Consider using Laravel Octane for even better performance"
