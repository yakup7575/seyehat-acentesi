# Seyahat Acentesi Marketplace

WordPress tabanlı seyahat acentesi pazaryeri sitesi. Bu proje, seyahat acentelerinin turlarını paylaşabileceği ve müşterilerin güvenle rezervasyon yapabileceği kapsamlı bir marketplace sistemidir.

## Özellikler

### 🏢 Partner (Acente) Yönetimi
- Seyahat acentesi kayıt sistemi
- Belge yükleme ve doğrulama
- Admin onay süreci
- Partner dashboard

### 🌍 Tur Yönetimi
- Kapsamlı tur ekleme sistemi
- Tur kategorileri (Yurtiçi, Yurtdışı, Yunan Adaları vb.)
- Fiyat ve kapasite yönetimi
- Tur detay sayfaları

### 💳 WooCommerce Entegrasyonu
- Güvenli ödeme sistemi
- Sepet ve checkout süreci
- Rezervasyon yönetimi
- Sipariş takibi

### 👥 Kullanıcı Rolleri
- **Admin**: Tüm sistem yönetimi
- **Partner**: Tur ekleme ve yönetme
- **Müşteri**: Tur arama ve rezervasyon

### 🎨 Modern Tasarım
- Mobil uyumlu responsive tasarım
- Kullanıcı dostu arayüz
- Modern CSS ve JavaScript

## Kurulum

### Gereksinimler
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.7+
- WooCommerce 6.0+ (önerilir)

### Kurulum Adımları

1. **Dosyaları Yükleyin**
   ```bash
   # WordPress dizininize dosyaları kopyalayın
   cp -r wp-content/ /path/to/wordpress/
   ```

2. **Temayı Aktif Edin**
   - WordPress Admin Panel > Görünüm > Temalar
   - "Seyahat Acentesi Marketplace" temasını aktif edin

3. **Eklentiyi Aktif Edin**
   - WordPress Admin Panel > Eklentiler
   - "Seyahat Acentesi Marketplace" eklentisini aktif edin

4. **WooCommerce'i Kurun (Opsiyonel)**
   - Eklentiler > Yeni Ekle > "WooCommerce" arayın ve kurun
   - WooCommerce kurulum sihirbazını tamamlayın

5. **Örnek Verileri Yükleyin**
   - Admin Panel > Marketplace menüsüne gidin
   - "Örnek Verileri Yükle" butonuna tıklayın

## Kullanım

### Admin İşlemleri

#### Partner Başvurularını Yönetme
1. Admin Panel > Marketplace > Partner Başvuruları
2. Bekleyen başvuruları inceleyin
3. Belgeleri kontrol edin
4. Onayla veya reddet

#### Tur Moderasyonu
1. Admin Panel > Marketplace > Tur Yönetimi
2. Yeni eklenen turları inceleyin
3. Gerekirse düzenleyin veya yayından kaldırın

### Partner İşlemleri

#### Partner Kayıt
1. Site > Partner Kayıt sayfasına gidin
2. Şirket bilgilerini doldurun
3. Gerekli belgeleri yükleyin
4. Başvuruyu gönderin

#### Tur Ekleme
1. Partner Dashboard'a giriş yapın
2. "Yeni Tur Ekle" butonuna tıklayın
3. Tur detaylarını doldurun
4. Yayınlayın

### Müşteri İşlemleri

#### Tur Arama
1. Ana sayfa veya Turlar sayfasını ziyaret edin
2. Filtreleri kullanarak arama yapın
3. İstediğiniz turu seçin

#### Rezervasyon
1. Tur detay sayfasında rezervasyon formunu doldurun
2. Sepete ekleyin
3. Ödeme işlemini tamamlayın

## Dosya Yapısı

```
wp-content/
├── themes/seyahat-acentesi/          # Ana tema
│   ├── style.css                     # Ana CSS dosyası
│   ├── functions.php                 # Tema fonksiyonları
│   ├── index.php                     # Ana sayfa
│   ├── header.php                    # Site başlığı
│   ├── footer.php                    # Site alt bilgisi
│   ├── archive-tour.php              # Tur listesi
│   ├── single-tour.php               # Tur detay sayfası
│   └── js/script.js                  # Tema JavaScript
│
└── plugins/seyahat-acentesi-marketplace/  # Ana eklenti
    ├── seyahat-acentesi-marketplace.php   # Ana eklenti dosyası
    ├── includes/                           # İlave dosyalar
    │   ├── woocommerce-integration.php     # WooCommerce entegrasyonu
    │   └── sample-data.php                 # Örnek veri yükleyici
    ├── templates/                          # Şablon dosyaları
    │   ├── admin/                          # Admin paneli şablonları
    │   └── frontend/                       # Ön yüz şablonları
    └── assets/                             # CSS ve JS dosyaları
        ├── css/
        └── js/
```

## Veritabanı Tabloları

Eklenti otomatik olarak aşağıdaki tabloları oluşturur:

- `wp_partner_applications`: Partner başvuru bilgileri
- `wp_tour_bookings`: Tur rezervasyon kayıtları

## Özelleştirme

### Tema Özelleştirme
- `wp-content/themes/seyahat-acentesi/style.css` dosyasını düzenleyin
- Yeni şablon dosyaları ekleyin
- `functions.php` dosyasına yeni özellikler ekleyin

### Eklenti Özelleştirme
- `wp-content/plugins/seyahat-acentesi-marketplace/` dizinindeki dosyaları düzenleyin
- Yeni hook'lar ve filtreler ekleyin
- Admin paneline yeni özellikler ekleyin

## Shortcode'lar

Aşağıdaki shortcode'ları sayfalarda kullanabilirsiniz:

- `[partner_registration_form]`: Partner kayıt formu
- `[partner_dashboard]`: Partner kontrol paneli
- `[tour_search]`: Tur arama formu

## Güvenlik

- Tüm kullanıcı girişleri sanitize edilir
- CSRF koruması için nonce kullanılır
- Dosya yükleme güvenlik kontrolü
- SQL injection koruması

## Performans

- Optimize edilmiş veritabanı sorguları
- CSS ve JavaScript minifikasyonu önerilir
- Önbellekleme eklentileri desteklenir
- Görseller için lazy loading

## Destek

Herhangi bir sorun yaşarsanız:

1. WordPress ve eklenti güncel mi kontrol edin
2. Hata loglarını kontrol edin
3. Çakışan eklentileri deaktif edin
4. GitHub'da issue açın

## Lisans

Bu proje GPL v2 lisansı altında yayınlanmıştır.

## Katkıda Bulunma

1. Repository'yi fork edin
2. Yeni bir branch oluşturun
3. Değişikliklerinizi commit edin
4. Pull request gönderin

## Yol Haritası

- [ ] Çoklu dil desteği
- [ ] Mobil uygulama API'si
- [ ] Gelişmiş raporlama
- [ ] Sosyal medya entegrasyonu
- [ ] Otomatik e-posta pazarlama
- [ ] Müşteri puanlama sistemi