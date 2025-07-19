# Seyahat Acentesi - Travel Marketplace Platform

GetYourGuide benzeri tam kapsamlı seyahat pazaryeri platformu. WordPress ve WooCommerce tabanlı geliştirilmiş çok satıcılı marketplace sistemi.

## Özellikler

### ✅ Temel Altyapı (Tamamlandı)
- WordPress teması (Seyahat Theme)
- Marketplace eklentisi (Seyahat Marketplace)
- Responsive tasarım (Bootstrap 5)
- Çok dilli destek (TR/EN)

### ✅ Multi-Vendor Sistemi (Tamamlandı)
- Satıcı kayıt sistemi
- Satıcı dashboard'u
- Komisyon yönetimi
- KYC doğrulama sistemi

### ✅ Tur/Aktivite Yönetimi (Tamamlandı)
- Özel post türleri (seyahat_tour, seyahat_vendor, seyahat_booking)
- Kategoriler ve destinasyonlar
- Detaylı tur bilgileri
- Fiyatlandırma sistemi

### ✅ Rezervasyon Sistemi (Tamamlandı)
- AJAX tabanlı rezervasyon
- Gerçek zamanlı müsaitlik
- Otomatik komisyon hesaplama
- Rezervasyon yönetimi

### 🚧 Devam Eden Özellikler
- Ödeme entegrasyonları (Stripe, PayPal, iyzico)
- Gelişmiş arama ve filtreleme
- Harita entegrasyonu
- Email bildirimler

### 📋 Planlanmış Özellikler
- PWA (Progressive Web App)
- Mobil uygulama API
- Analitik dashboard
- SEO optimizasyonu
- Çoklu para birimi

## Teknoloji Stack

- **WordPress**: 6.0+
- **WooCommerce**: Pro
- **Frontend**: Bootstrap 5, jQuery
- **Database**: MySQL 8.0+
- **API**: REST API

## Kurulum

1. WordPress kurulumu yapın
2. `wp-content/themes/seyahat-theme` klasörünü themes dizinine kopyalayın
3. `wp-content/plugins/seyahat-marketplace` klasörünü plugins dizinine kopyalayın
4. Admin panelden tema ve eklentiyi aktifleştirin
5. Demo içeriği için `demo-content.sql` dosyasını import edin

## Dosya Yapısı

```
├── wp-config.php                          # WordPress yapılandırması
├── wp-content/
│   ├── themes/seyahat-theme/               # Ana tema
│   │   ├── functions.php                   # Tema fonksiyonları
│   │   ├── style.css                       # Ana CSS
│   │   ├── index.php                       # Ana şablon
│   │   ├── header.php                      # Başlık şablonu
│   │   ├── footer.php                      # Alt bilgi şablonu
│   │   └── assets/                         # CSS/JS dosyaları
│   └── plugins/seyahat-marketplace/        # Marketplace eklentisi
│       ├── seyahat-marketplace.php         # Ana eklenti dosyası
│       ├── includes/                       # Core sınıflar
│       ├── admin/                          # Admin paneli
│       ├── public/                         # Frontend
│       └── assets/                         # CSS/JS dosyaları
├── demo-content.sql                        # Demo içerik
└── README.md                               # Bu dosya
```

## Kullanım

### Satıcı Olarak Katılım
1. Ana sayfadan "Satıcı Ol" linkini tıklayın
2. Kayıt formunu doldurun
3. Admin onayını bekleyin
4. Onay sonrası dashboard'a erişin

### Tur Ekleme
1. Satıcı dashboard'una giriş yapın
2. "Yeni Tur Ekle" butonunu tıklayın
3. Tur detaylarını doldurun
4. Kaydedin ve yayınlayın

### Rezervasyon Yapma
1. Tur detay sayfasını açın
2. "Rezervasyon Yap" butonunu tıklayın
3. Tarih ve misafir sayısını seçin
4. Ödeme işlemini tamamlayın

## API Kullanımı

### Turları Listele
```http
GET /wp-json/seyahat/v1/tours
```

### Tek Tur Detayı
```http
GET /wp-json/seyahat/v1/tours/{id}
```

### Rezervasyon Oluştur
```http
POST /wp-json/seyahat/v1/bookings
```

## Katkıda Bulunma

1. Fork'layın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit'leyin (`git commit -m 'Add amazing feature'`)
4. Push'layın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## Lisans

GPL v2 or later

## İletişim

Proje ile ilgili sorularınız için GitHub Issues kullanın.
