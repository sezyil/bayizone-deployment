# SSL Sertifikaları

Bu dizine aşağıdaki SSL sertifikalarını yerleştirin:

## Gerekli Dosyalar

1. **bayizone.com.crt** - SSL sertifikası
2. **bayizone.com.key** - Private key

## SSL Sertifikası Alma

### Let's Encrypt (Ücretsiz)
```bash
# Certbot ile otomatik SSL
sudo certbot certonly --standalone -d app.bayizone.com -d provider.bayizone.com -d store.bayizone.com
```

### Manuel SSL
1. Domain sağlayıcınızdan SSL sertifikası alın
2. .crt ve .key dosyalarını bu dizine kopyalayın

## Geçici SSL (Development)
```bash
# Self-signed certificate oluştur
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout bayizone.com.key \
  -out bayizone.com.crt \
  -subj "/C=TR/ST=Istanbul/L=Istanbul/O=Bayizone/CN=bayizone.com"
```

## Nginx Konfigürasyonu
SSL sertifikaları otomatik olarak Nginx tarafından kullanılacaktır. 