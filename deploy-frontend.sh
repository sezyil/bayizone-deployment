#!/bin/bash

echo "🚀 Bayizone Frontend Deployment Başlıyor..."

# Değişkenler
PROVIDER_DIR="bayi-provider-panel"
STORE_DIR="bayi-store"
CLIENT_DIR="bayi-client-panel"
API_URL="https://app.bayizone.com"

# Provider Panel Deployment
echo "🏢 Provider Panel build ediliyor..."
cd $PROVIDER_DIR

echo "📦 Dependencies yükleniyor..."
npm install

echo "🔧 Environment ayarları..."
echo "VITE_API_URL=$API_URL" > .env
echo "VITE_APP_MODE=production" >> .env

echo "🏗️ Production build..."
npm run build

echo "✅ Provider Panel build tamamlandı!"
echo "🌐 Domain: https://provider.bayizone.com"

# Store Deployment
echo "🛍️ Store build ediliyor..."
cd ../$STORE_DIR

echo "📦 Dependencies yükleniyor..."
npm install

echo "🔧 Environment ayarları..."
echo "VITE_API_URL=$API_URL" > .env
echo "VITE_APP_MODE=production" >> .env

echo "🏗️ Production build..."
npm run build

echo "✅ Store build tamamlandı!"
echo "🌐 Domain: https://store.bayizone.com"

# Client Panel Deployment
echo "👥 Client Panel build ediliyor..."
cd ../$CLIENT_DIR

echo "📦 Dependencies yükleniyor..."
npm install

echo "🔧 Environment ayarları..."
echo "VITE_API_URL=$API_URL" > .env
echo "VITE_APP_MODE=production" >> .env

echo "🏗️ Production build..."
npm run build

echo "✅ Client Panel build tamamlandı!"
echo "🌐 Domain: https://app.bayizone.com"

echo "🎉 Tüm frontend uygulamaları build edildi!"
echo ""
echo "📋 Deployment Özeti:"
echo "🏢 Provider Panel: https://provider.bayizone.com"
echo "🛍️ Store: https://store.bayizone.com"
echo "👥 Client Panel: https://app.bayizone.com"
echo "🔗 API: https://app.bayizone.com" 