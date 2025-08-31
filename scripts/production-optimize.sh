#!/bin/bash

# eForum Production Optimization Script
# This script optimizes the application for production deployment

set -e

echo "🚀 Starting eForum Production Optimization..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

# Set production environment
export APP_ENV=production
export APP_DEBUG=false

echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader
npm install --production
npm run build

echo "🗄️  Running database migrations..."
php artisan migrate --force

echo "⚙️  Optimizing configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "🔗 Creating storage link..."
php artisan storage:link

echo "📁 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 755 public/uploads

echo "🧹 Clearing old caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "⚡ Re-optimizing after cache clear..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "✅ Production optimization completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Configure your web server (Nginx/Apache)"
echo "2. Set up SSL certificate"
echo "3. Configure queue worker: php artisan queue:work"
echo "4. Set up monitoring (Sentry, etc.)"
echo "5. Configure backup strategy"
echo ""
echo "🔒 Security checklist:"
echo "- [ ] APP_DEBUG=false in .env"
echo "- [ ] Strong database password"
echo "- [ ] SSL certificate installed"
echo "- [ ] Firewall rules configured"
echo "- [ ] File permissions set correctly"
