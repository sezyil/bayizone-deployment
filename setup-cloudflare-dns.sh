#!/bin/bash

echo "🌐 Cloudflare DNS Ayarları Kuruluyor..."

# Cloudflare API Token
CLOUDFLARE_TOKEN="iSwslw27ixpnbJPn3uobDD7_M2Gh9FnGPHjL9gNj"
DOMAIN="bayizone.com"

# Renk kodları
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

# Zone ID'yi al
echo "🔍 Zone ID alınıyor..."
ZONE_ID=$(curl -s -X GET "https://api.cloudflare.com/client/v4/zones?name=$DOMAIN" \
  -H "Authorization: Bearer $CLOUDFLARE_TOKEN" \
  -H "Content-Type: application/json" | jq -r '.result[0].id')

if [ "$ZONE_ID" = "null" ] || [ -z "$ZONE_ID" ]; then
    echo -e "${RED}❌ Domain bulunamadı: $DOMAIN${NC}"
    echo "Lütfen domain'in Cloudflare'de kayıtlı olduğundan emin olun."
    exit 1
fi

echo -e "${GREEN}✅ Zone ID: $ZONE_ID${NC}"

# DNS kayıtları oluştur
echo "📝 DNS kayıtları oluşturuluyor..."

# A kaydı - app.bayizone.com (ana uygulama)
echo "🌐 app.bayizone.com A kaydı..."
curl -s -X POST "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/dns_records" \
  -H "Authorization: Bearer $CLOUDFLARE_TOKEN" \
  -H "Content-Type: application/json" \
  --data '{
    "type": "A",
    "name": "app",
    "content": "YOUR_DROPLET_IP",
    "ttl": 1,
    "proxied": true
  }' | jq -r '.success'

# A kaydı - provider.bayizone.com
echo "🏢 provider.bayizone.com A kaydı..."
curl -s -X POST "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/dns_records" \
  -H "Authorization: Bearer $CLOUDFLARE_TOKEN" \
  -H "Content-Type: application/json" \
  --data '{
    "type": "A",
    "name": "provider",
    "content": "YOUR_DROPLET_IP",
    "ttl": 1,
    "proxied": true
  }' | jq -r '.success'

# A kaydı - store.bayizone.com
echo "🛍️ store.bayizone.com A kaydı..."
curl -s -X POST "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/dns_records" \
  -H "Authorization: Bearer $CLOUDFLARE_TOKEN" \
  -H "Content-Type: application/json" \
  --data '{
    "type": "A",
    "name": "store",
    "content": "YOUR_DROPLET_IP",
    "ttl": 1,
    "proxied": true
  }' | jq -r '.success'

# CNAME kaydı - www.bayizone.com -> app.bayizone.com
echo "🌍 www.bayizone.com CNAME kaydı..."
curl -s -X POST "https://api.cloudflare.com/client/v4/zones/$ZONE_ID/dns_records" \
  -H "Authorization: Bearer $CLOUDFLARE_TOKEN" \
  -H "Content-Type: application/json" \
  --data '{
    "type": "CNAME",
    "name": "www",
    "content": "app.bayizone.com",
    "ttl": 1,
    "proxied": true
  }' | jq -r '.success'

echo -e "${GREEN}✅ DNS kayıtları oluşturuldu!${NC}"
echo ""
echo "📋 Oluşturulan kayıtlar:"
echo "🌐 app.bayizone.com -> YOUR_DROPLET_IP"
echo "🏢 provider.bayizone.com -> YOUR_DROPLET_IP"
echo "🛍️ store.bayizone.com -> YOUR_DROPLET_IP"
echo "🌍 www.bayizone.com -> app.bayizone.com"
echo ""
echo "⚠️  Lütfen YOUR_DROPLET_IP yerine DigitalOcean droplet'inizin IP adresini yazın!"
echo "💡 IP adresini öğrenmek için: DigitalOcean Dashboard > Droplets > Your Droplet" 