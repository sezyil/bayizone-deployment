#!/bin/bash

echo "🚀 Bayizone Full Deployment Başlıyor..."
echo "========================================"

# Renk kodları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonksiyonlar
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Gerekli araçları kontrol et
check_requirements() {
    log_info "Gerekli araçlar kontrol ediliyor..."
    
    if ! command -v docker &> /dev/null; then
        log_error "Docker bulunamadı! Lütfen Docker'ı yükleyin."
        exit 1
    fi
    
    if ! command -v node &> /dev/null; then
        log_error "Node.js bulunamadı! Lütfen Node.js 18+ yükleyin."
        exit 1
    fi
    
    if ! command -v npm &> /dev/null; then
        log_error "npm bulunamadı! Lütfen npm yükleyin."
        exit 1
    fi
    
    log_success "Tüm gerekli araçlar mevcut!"
}

# Frontend build
build_frontend() {
    log_info "Frontend uygulamaları build ediliyor..."
    
    # Provider Panel
    log_info "Provider Panel build ediliyor..."
    cd bayi-provider-panel
    npm install
    echo "VITE_API_URL=https://app.bayizone.com" > .env
    echo "VITE_APP_MODE=production" >> .env
    npm run build
    cd ..
    log_success "Provider Panel build tamamlandı!"
    
    # Store
    log_info "Store build ediliyor..."
    cd bayi-store
    npm install
    echo "VITE_API_URL=https://app.bayizone.com" > .env
    echo "VITE_APP_MODE=production" >> .env
    npm run build
    cd ..
    log_success "Store build tamamlandı!"
    
    # Client Panel
    log_info "Client Panel build ediliyor..."
    cd bayi-client-panel
    npm install
    echo "VITE_API_URL=https://app.bayizone.com" > .env
    echo "VITE_APP_MODE=production" >> .env
    npm run build
    cd ..
    log_success "Client Panel build tamamlandı!"
}

# Backend setup
setup_backend() {
    log_info "Backend hazırlanıyor..."
    
    cd bayi-backend
    
    # Composer dependencies
    log_info "Composer dependencies yükleniyor..."
    composer install --no-dev --optimize-autoloader
    
    # Environment setup
    if [ ! -f .env ]; then
        cp .env.example .env
        log_success ".env dosyası oluşturuldu"
    fi
    
    # Update .env for production
    sed -i '' 's/APP_ENV=local/APP_ENV=production/' .env
    sed -i '' 's/APP_DEBUG=true/APP_DEBUG=false/' .env
    sed -i '' 's/APP_URL=http:\/\/localhost/APP_URL=https:\/\/app.bayizone.com/' .env
    sed -i '' 's/DB_HOST=127.0.0.1/DB_HOST=mysql/' .env
    sed -i '' 's/DB_DATABASE=bayi_sistemi/DB_DATABASE=bayi_sistemi/' .env
    sed -i '' 's/DB_USERNAME=root/DB_USERNAME=bayi_user/' .env
    sed -i '' 's/DB_PASSWORD=/DB_PASSWORD=bayi_password_2024/' .env
    
    # Laravel commands
    log_info "Laravel komutları çalıştırılıyor..."
    php artisan key:generate
    php artisan migrate --force
    php artisan db:seed --force
    php artisan command:rebuild-permissions
    php artisan storage:link
    
    # Cache optimization
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    cd ..
    log_success "Backend hazırlandı!"
}

# Docker deployment
deploy_docker() {
    log_info "Docker container'ları başlatılıyor..."
    
    # SSL sertifikaları için dizin oluştur
    mkdir -p ssl
    
    # Docker Compose ile başlat
    docker compose -f docker-compose.prod.yml up -d
    
    log_success "Docker deployment tamamlandı!"
}

# Health check
health_check() {
    log_info "Health check yapılıyor..."
    
    # Backend health check
    sleep 10
    if curl -f http://localhost:8000/api/health > /dev/null 2>&1; then
        log_success "Backend çalışıyor!"
    else
        log_warning "Backend henüz hazır değil, biraz bekleyin..."
    fi
    
    # Nginx health check
    if curl -f http://localhost > /dev/null 2>&1; then
        log_success "Nginx çalışıyor!"
    else
        log_error "Nginx başlatılamadı!"
    fi
}

# Ana fonksiyon
main() {
    echo "🎯 Bayizone Deployment Script"
    echo "=============================="
    
    check_requirements
    build_frontend
    setup_backend
    deploy_docker
    health_check
    
    echo ""
    echo "🎉 Deployment tamamlandı!"
    echo "=========================="
    echo "🌐 Ana Uygulama: https://app.bayizone.com"
    echo "🏢 Provider Panel: https://provider.bayizone.com"
    echo "🛍️ Store: https://store.bayizone.com"
    echo "🔗 API: https://app.bayizone.com/api"
    echo ""
    echo "📋 Kontrol komutları:"
    echo "docker ps -a"
    echo "docker logs bayizone-backend"
    echo "docker logs bayizone-nginx"
    echo ""
    echo "🔧 SSL sertifikalarını /ssl dizinine yerleştirin"
    echo "📝 Domain DNS ayarlarını kontrol edin"
}

# Script'i çalıştır
main 