# Restoran QR Menü Yönetim Sistemi

Bu proje, restoranlar için QR kod tabanlı bir menü yönetim sistemi sağlar. Kullanıcılar ürünleri ve kategorileri görüntüleyebilir, arama yapabilir ve yönetim paneli üzerinden içerikleri düzenleyebilir.

## Özellikler

- **Ürün Yönetimi**: Ürün ekleme, düzenleme, silme ve fotoğraf yönetimi.
- **Kategori Yönetimi**: Kategori ekleme, düzenleme, silme ve fotoğraf yönetimi.
- **Arama Fonksiyonu**: Ürünler arasında hızlı arama yapma.
- **Admin Paneli**: Kullanıcı adı ve şifre güncelleme, site ayarlarını düzenleme.
- **Bakım Modu**: Siteyi bakım moduna alma seçeneği.
- **Responsive Tasarım**: Mobil cihazlar için optimize edilmiş kullanıcı arayüzü.

## Kurulum

1. **Depoyu Klonlayın**:
   ```bash
   git clone https://github.com/kullaniciadi/restoran-qr-menu.git
   ```

2. **Veritabanını Ayarlayın**:
   - `database.sql` dosyasını MySQL veritabanınıza içe aktarın.
   - `inc/main.php` dosyasındaki veritabanı bağlantı ayarlarını düzenleyin:
     ```php
     $db = new PDO('mysql:host=localhost;dbname=veritabani_adi;charset=utf8', 'kullanici_adi', 'sifre');
     ```

3. **Yükleme Dizini**:
   - `uploads` klasörünün yazılabilir olduğundan emin olun:
     ```bash
     chmod -R 777 uploads
     ```

4. **XAMPP Üzerinde Çalıştırın**:
   - Projeyi `htdocs` dizinine taşıyın.
   - Tarayıcınızda `http://localhost/restoran-qr-menu` adresine gidin.

## Kullanım

### Admin Paneli
- **Giriş Yapın**: Admin paneline giriş yapmak için varsayılan kullanıcı adı ve şifreyi kullanın:
  - Kullanıcı Adı: `admin`
  - Şifre: `admin`
- **Ürün ve Kategori Yönetimi**: Ürün ve kategorileri ekleyin, düzenleyin veya silin.
- **Ayarlar**: Site başlığını ve logosunu değiştirin, bakım modunu etkinleştirin.

### Kullanıcı Arayüzü
- **Ürün Görüntüleme**: QR kod ile ürün detaylarına erişin.
- **Kategori Görüntüleme**: Kategorilere göz atın ve ürünleri listeleyin.
- **Arama**: Ürünler arasında hızlıca arama yapın.

## Teknolojiler

- **Backend**: PHP (PDO ile MySQL bağlantısı)
- **Frontend**: HTML, CSS, Bootstrap, JavaScript
- **Veritabanı**: MySQL
- **Diğer**: Swiper.js (slider için)

## Ekran Görüntüleri

### Ana Sayfa
![Ana Sayfa](uploads/screenshots/homepage.png)

### Admin Paneli
![Admin Paneli](uploads/screenshots/admin-panel.png)

## Katkıda Bulunma

1. Bu projeyi forklayın.
2. Yeni bir dal oluşturun: `git checkout -b yeni-ozellik`.
3. Değişikliklerinizi yapın ve commit edin: `git commit -m 'Yeni özellik eklendi'`.
4. Dalınızı push edin: `git push origin yeni-ozellik`.
5. Bir Pull Request açın.

## Lisans

Bu proje MIT Lisansı ile lisanslanmıştır. Daha fazla bilgi için `LICENSE` dosyasına bakabilirsiniz.

## İletişim

Herhangi bir sorunuz veya öneriniz varsa, lütfen benimle iletişime geçin:

- **E-posta**: [email@example.com](mailto:email@example.com)
- **GitHub**: [github.com/kullaniciadi](https://github.com/kullaniciadi)