#!/bin/bash

echo "🚀 Bayizone Backend Deployment Başlıyor..."

# Değişkenler
BACKEND_DIR="bayi-backend"
DOMAIN="app.bayizone.com"

# Backend dizinine git
cd $BACKEND_DIR

echo "📦 Composer dependencies yükleniyor..."
composer install --no-dev --optimize-autoloader

echo "🔧 Environment dosyası hazırlanıyor..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ .env dosyası oluşturuldu"
fi

echo "🔑 Application key generate ediliyor..."
php artisan key:generate

echo "🗄️ Database migration çalıştırılıyor..."
php artisan migrate --force

echo "📊 Database seeder çalıştırılıyor..."
php artisan db:seed --force

echo "🔒 Permission'lar rebuild ediliyor..."
php artisan command:rebuild-permissions

echo "📁 Storage link oluşturuluyor..."
php artisan storage:link

echo "🧹 Cache temizleniyor..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "⚡ Production optimizasyonları..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🐳 Docker container başlatılıyor..."
docker-compose up -d

echo "✅ Backend deployment tamamlandı!"
echo "🌐 API Endpoint: https://$DOMAIN"
echo "📊 Health Check: https://$DOMAIN/api/health" 