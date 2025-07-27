export const subscriptionNameConverter = (name: any) => {
    switch (name) {
        case "user_count":
            return "Kullanıcı Sayısı";
        case "catalog_management":
            return "Ürün Yönetimi";
        case "proforma_invoices":
            return "Proforma Fatura";
        case "create_proposal":
            return "Talepten Teklif Oluştur";
        case "proposal_management":
            return "Teklif Yönetimi";
        case "sales_management":
            return "Satış Yönetimi";
        case "whatsapp_integration":
            return "Whatsapp Entegrasyonu";
        case "send_proposal_with_link":
            return "Link ile Teklif Gönderme";
        case "simple_accounting":
            return "Basit Muhasebe";
        default:
            return "Bilinmeyen";
    }
}