# DigitalOcean Droplet Kurulum Rehberi

## 🎯 Önerilen Plan

**Plan:** Basic - Premium Intel - $16/mo
- **RAM:** 2 GB
- **CPU:** 1 Intel CPU
- **Disk:** 70 GB NVMe SSD
- **Transfer:** 2 TB

## 📋 Kurulum Adımları

### 1. Droplet Oluşturma

1. **Choose an image:**
   - ✅ Ubuntu
   - ✅ Version: 25.04 x64

2. **Choose Size:**
   - ✅ Basic
   - ✅ Premium Intel
   - ✅ $16/mo plan

3. **Choose a datacenter region:**
   - ✅ Frankfurt (fra1) - Türkiye'ye en yakın

4. **Authentication:**
   - ✅ SSH key ekleyin (önerilen)
   - Veya Password authentication

5. **Finalize and create:**
   - Hostname: `bayizone-server`
   - Create Droplet

### 2. IP Adresini Not Edin

Droplet oluşturulduktan sonra IP adresini not edin:
```
🌐 IP Adresi: xxx.xxx.xxx.xxx
```

### 3. DNS Ayarları

IP adresini aldıktan sonra:

```bash
# Script'i çalıştırılabilir yap
chmod +x setup-cloudflare-dns.sh

# IP adresini güncelle ve çalıştır
sed -i '' 's/YOUR_DROPLET_IP/xxx.xxx.xxx.xxx/g' setup-cloudflare-dns.sh
./setup-cloudflare-dns.sh
```

### 4. Server'a Bağlanma

```bash
# SSH ile bağlan
ssh root@xxx.xxx.xxx.xxx

# Veya SSH key ile
ssh -i ~/.ssh/your_key root@xxx.xxx.xxx.xxx
```

### 5. Server Hazırlığı

```bash
# Sistem güncellemesi
apt update && apt upgrade -y

# Docker kurulumu
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Docker Compose kurulumu
apt install docker-compose-plugin -y

# Firewall ayarları
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
```

### 6. Proje Deployment

```bash
# Proje dosyalarını server'a kopyala
# (Git clone veya SCP ile)

# Deployment'ı başlat
./deploy-all.sh
```

## 🔧 Güvenlik Ayarları

### SSH Güvenliği
```bash
# SSH port değiştirme (opsiyonel)
sed -i 's/#Port 22/Port 2222/' /etc/ssh/sshd_config
systemctl restart sshd

# Root login'i kapatma
sed -i 's/#PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
```

### Firewall
```bash
# Sadece gerekli portları aç
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
```

## 📊 Monitoring

### Disk Kullanımı
```bash
df -h
```

### Memory Kullanımı
```bash
free -h
```

### Docker Container'ları
```bash
docker ps
docker stats
```

## 🚀 Otomatik Backup

### Cron Job Oluşturma
```bash
# Backup script'i oluştur
cat > /root/backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker exec bayizone-mysql mysqldump -u bayi_user -pbayi_password_2024 bayi_sistemi > /backup/db_$DATE.sql
tar -czf /backup/files_$DATE.tar.gz /var/www/bayi-backend/storage
find /backup -name "*.sql" -mtime +7 -delete
find /backup -name "*.tar.gz" -mtime +7 -delete
EOF

chmod +x /root/backup.sh

# Günlük backup
echo "0 2 * * * /root/backup.sh" | crontab -
```

## 💰 Maliyet Analizi

- **Droplet:** $16/ay
- **Domain:** ~$10-15/yıl
- **SSL:** Ücretsiz (Let's Encrypt)
- **Backup:** Dahil

**Toplam:** ~$16/ay + domain maliyeti 