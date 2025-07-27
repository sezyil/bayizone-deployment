# 🚀 Bayizone Full Stack Application

Bu repository Bayizone uygulamasının tam stack deployment'ını içerir.

## 📁 Proje Yapısı

```
bayizone/
├── bayi-backend/          # Laravel API (Backend)
├── bayi-provider-panel/   # Vue.js Provider Panel
├── bayi-store/           # Vue.js B2B Store
├── bayi-client-panel/    # Vue.js Client Panel
├── docker-compose.prod.yml
├── nginx-config.conf
└── deploy-all.sh
```

## 🌐 Uygulamalar

- **Backend API:** Laravel 10 + MySQL
- **Provider Panel:** Vue.js 3 + Vite
- **Store:** Vue.js 3 + Vite (B2B Mağaza)
- **Client Panel:** Vue.js 3 + Vite

## 🚀 Railway Deployment

### Environment Variables

Railway'de aşağıdaki environment variables'ları ayarlayın:

```env
# Laravel
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-app-key
APP_URL=https://your-domain.railway.app

# Database
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379
```

### Deployment Adımları

1. Railway'de yeni proje oluşturun
2. Bu repository'yi bağlayın
3. Environment variables'ları ayarlayın
4. Deploy edin

## 🔧 Local Development

```bash
# Backend
cd bayi-backend
composer install
php artisan serve

# Frontend (her biri için)
cd bayi-provider-panel
npm install
npm run dev
```

## 📊 API Endpoints

- `GET /api/utilities/countries` - Ülkeler listesi
- `GET /api/utilities/currencies` - Para birimleri
- `POST /api/client/auth/login` - Giriş
- `GET /api/client/products` - Ürünler

## 🛠️ Teknolojiler

- **Backend:** Laravel 10, PHP 8.3, MySQL 8.0
- **Frontend:** Vue.js 3, Vite, TypeScript
- **Cache:** Redis
- **Deployment:** Railway
- **Reverse Proxy:** Nginx 