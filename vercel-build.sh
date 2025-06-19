#!/bin/bash

# Install dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Copy .env for Laravel
echo "Setting up environment..."
cp .env.production .env 2>/dev/null || echo "Using default environment"

# Generate app key jika belum ada
if ! grep -q "^APP_KEY=" .env; then
  php artisan key:generate
fi

# Caching konfigurasi Laravel
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Buat symlink untuk public
echo "Setting up public directory..."
# Tidak perlu membuat direktori baru karena sudah ada

echo "Build completed!" 