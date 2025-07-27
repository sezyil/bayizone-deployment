# Bayizone Deployment Planı

## Proje Yapısı

### 1. Backend (Laravel API)
- **Repo**: bayi-backend
- **Teknoloji**: Laravel 10, PHP 8.1+
- **Database**: MySQL 8.0
- **Domain**: app.bayizone.com (API endpoint)

### 2. Provider Panel (Vue.js)
- **Repo**: bayi-provider-panel  
- **Teknoloji**: Vue 3, Vite, TypeScript
- **Domain**: provider.bayizone.com
- **Bağımlılık**: Backend API

### 3. Store (B2B Mağaza)
- **Repo**: bayi-store
- **Teknoloji**: Vue 3, Vite, TypeScript
- **Domain**: store.bayizone.com
- **Bağımlılık**: Backend API

### 4. Client Panel (Vue.js)
- **Repo**: bayi-client-panel
- **Teknoloji**: Vue 3, Vite, TypeScript
- **Domain**: app.bayizone.com (ana uygulama)
- **Bağımlılık**: Backend API

## Deployment Stratejisi

### Aşama 1: Backend Deployment
1. Laravel uygulamasını deploy et
2. MySQL veritabanını kur
3. Environment değişkenlerini ayarla
4. API endpoint'lerini test et

### Aşama 2: Frontend Uygulamaları
1. Her Vue.js uygulamasını build et
2. Static hosting'e deploy et
3. Domain yönlendirmelerini ayarla
4. API bağlantılarını test et

### Aşama 3: Domain ve SSL
1. Domain kayıtlarını ayarla
2. SSL sertifikalarını kur
3. CORS ayarlarını yapılandır

## Gerekli Araçlar
- Docker (backend için)
- Node.js 18+ (frontend build için)
- MySQL 8.0
- Nginx (reverse proxy)
- SSL sertifikaları

## Environment Değişkenleri
- Backend API URL'leri
- Database bağlantıları
- Mail ayarları
- File storage ayarları 