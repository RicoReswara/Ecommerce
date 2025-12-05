# 🛍️ E-Commerce Platform dengan Midtrans Payment Gateway

Platform e-commerce modern dengan sistem pembayaran terintegrasi Midtrans, responsive design, dan admin dashboard lengkap.

---

## 📋 Daftar Isi

1. [📋 Prasyarat](#-prasyarat)
2. [🚀 Instalasi Cepat](#-instalasi-cepat)
3. [🗂️ Struktur File & Manajemen Assets](#-struktur-file--manajemen-assets)
4. [💾 Setup Database](#-setup-database)
5. [💳 Konfigurasi Midtrans](#-konfigurasi-midtrans)
6. [⚙️ Konfigurasi Environment](#-konfigurasi-environment)
7. [🧪 Testing & Deployment](#-testing--deployment)
8. [📚 Dokumentasi Tambahan](#-dokumentasi-tambahan)
9. [❓ Troubleshooting](#-troubleshooting)

---

## 📋 Prasyarat

Sebelum memulai, pastikan sistem Anda memiliki:

### Server Requirements
- **PHP**: 7.4 atau lebih tinggi
- **MySQL**: 5.7 atau lebih tinggi
- **Web Server**: Apache atau Nginx (dengan mod_rewrite untuk Apache)
- **Composer**: Untuk manage PHP dependencies

### Koneksi Internet
- Akses ke Midtrans (payment gateway)
- Domain/Hosting yang sudah aktif (untuk production)

### Tools yang Digunakan
```bash
# Verifikasi instalasi PHP
php -v

# Verifikasi MySQL
mysql --version

# Verifikasi Composer
composer --version
```

---

## 🚀 Instalasi Cepat

### 1️⃣ Clone/Download Repository

```bash
# Jika dari Git
git clone <repository-url>
cd ecommerce

# Atau extract dari ZIP
unzip ecommerce.zip
cd ecommerce
```

### 2️⃣ Install Dependencies

```bash
# Install Midtrans PHP library
composer install

# Atau jika vendor belum ada
composer require midtrans/midtrans-php
```

### 3️⃣ Setup Database

Lihat section [💾 Setup Database](#-setup-database)

### 4️⃣ Konfigurasi Midtrans

Lihat section [💳 Konfigurasi Midtrans](#-konfigurasi-midtrans)

### 5️⃣ Konfigurasi Environment

Lihat section [⚙️ Konfigurasi Environment](#-konfigurasi-environment)

### 6️⃣ Set Permission (Linux/Mac)

```bash
# Izin write untuk folder uploads
chmod -R 755 product_images
chmod -R 755 category_images
chmod -R 755 img

# Atau lebih permisif jika perlu
chmod -R 777 product_images
chmod -R 777 category_images
chmod -R 777 img
```

### 7️⃣ Akses Website

```
Local Development:    http://localhost/ecommerce/
Production (contoh):  https://yourdomain.com/
```

---

## 🗂️ Struktur File & Manajemen Assets

### 📁 Struktur Direktori

```
ecommerce/
├── admin/                    # Admin panel
│   ├── header_admin.php
│   ├── footer_admin.php
│   ├── index.php
│   ├── products.php
│   ├── orders.php
│   ├── users.php
│   ├── add_product.php
│   ├── edit_product.php
│   └── categories.php
├── api/                      # API handlers
│   ├── auth_handler.php
│   ├── cart_handler.php
│   ├── admin_handler.php
│   └── midtrans_notification.php   # Webhook Midtrans
├── user/                     # User dashboard
│   ├── index.php
│   ├── profile.php
│   ├── orders.php
│   └── cart.php
├── css/                      # Stylesheet
│   └── style.css
├── js/                       # JavaScript
│   └── script.js
├── img/                      # Images (logo, banner, no-image)
│   ├── logo.png              # Logo aplikasi
│   ├── banner1.jpg           # Banner homepage
│   ├── no-image.jpg          # Gambar default produk
│   └── how-to.txt            # Dokumentasi upload image
├── product_images/           # Folder produk (auto-created)
├── category_images/          # Folder kategori (auto-created)
├── vendor/                   # Composer dependencies
│   └── midtrans/
├── db.php                    # Database connection
├── config.php                # General config
├── midtrans_config.php       # Midtrans configuration
├── header.php                # Header template
├── footer.php                # Footer template
├── index.php                 # Homepage
├── products.php              # Products listing
├── product_detail.php        # Product detail
├── cart.php                  # Shopping cart
├── checkout.php              # Checkout page
├── order_success.php         # Order confirmation
├── login.php                 # User login
├── register.php              # User registration
├── logout.php                # User logout
├── ecommerce2.sql            # Database schema
├── README.md                 # This file
├── .gitignore                # Git ignore rules
├── composer.json             # Composer config
└── composer.lock             # Composer lock file
```

---

### 🖼️ Manajemen Assets (Gambar, Logo, Banner)

#### **1. Logo** 
📍 **Lokasi**: `img/logo.png`

```
Spesifikasi:
- Nama file: logo.png
- Format: PNG (transparent background recommended)
- Dimensi: 200x60px (atau sesuaikan di CSS)
- Ukuran file: Max 500KB
- Tempat: Folder img/

Cara mengganti:
1. Siapkan file logo Anda (PNG/JPG)
2. Rename menjadi: logo.png
3. Letakkan di folder: img/
4. Replace file lama
5. Clear browser cache (Ctrl+F5)
```

#### **2. Banner Homepage**
📍 **Lokasi**: `img/banner1.jpg`

```
Spesifikasi:
- Nama file: banner1.jpg (bisa tambah banner2.jpg, banner3.jpg, dll)
- Format: JPG untuk foto, PNG untuk ilustrasi
- Dimensi: 1920x600px (responsive)
- Ukuran file: Max 1MB
- Tempat: Folder img/

Cara mengganti:
1. Siapkan file banner Anda
2. Rename menjadi: banner1.jpg
3. Letakkan di folder: img/
4. Edit index.php jika perlu tambah banner baru
5. Refresh halaman browser

Catatan:
- Jika ingin multiple banners, gunakan: banner1.jpg, banner2.jpg, dll
- Update code di index.php sesuai dengan nama banner baru
```

#### **3. No Image (Gambar Default Produk)**
📍 **Lokasi**: `img/no-image.jpg`

```
Spesifikasi:
- Nama file: no-image.jpg
- Format: JPG (atau sesuai format di code)
- Dimensi: 400x400px (square)
- Ukuran file: Max 500KB
- Tempat: Folder img/

Cara mengganti:
1. Siapkan gambar default Anda
2. Rename menjadi: no-image.jpg
3. Letakkan di folder: img/
4. Replacement otomatis jika produk tidak punya gambar
5. Clear cache browser
```

#### **4. Category Images** (Opsional)
📍 **Lokasi**: `category_images/` (auto-created)

```
Spesifikasi:
- Format: JPG/PNG
- Dimensi: 300x200px
- Ukuran file: Max 500KB per file
- Penamaan: category_<id>.jpg (misal: category_1.jpg)

Cara upload:
1. Folder category_images/ auto-created saat first product upload
2. Upload via admin panel → Kategori
3. Atau manual copy file ke folder category_images/
4. Penamaan otomatis: category_<category_id>.jpg
```

#### **5. Product Images**
📍 **Lokasi**: `product_images/` (auto-created)

```
Spesifikasi:
- Format: JPG/PNG/GIF
- Dimensi: Minimal 400x400px (optimal: 800x800px)
- Ukuran file: Max 5MB per file
- Penamaan: Auto-generated dengan timestamp

Cara upload:
1. Upload via admin panel → Tambah Produk
2. File disimpan otomatis di product_images/
3. Nama file: <hash>_<timestamp>.<ext>
4. Contoh: 68e8629a0fde3_1760060058.jpg

Tips:
- Compress image sebelum upload (quality tetap bagus)
- Gunakan tools: TinyPNG, ImageOptim, atau Compressor.io
- Semakin kecil ukuran, semakin cepat loading
```

---

## 💾 Setup Database

### 1️⃣ Create Database

**Via Command Line (Recommended)**:
```bash
# Login ke MySQL
mysql -u root -p

# Di MySQL terminal
CREATE DATABASE ecommerce2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce2;
```

**Via phpMyAdmin** (GUI):
1. Buka phpMyAdmin
2. Klik "New Database"
3. Database name: `ecommerce2`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### 2️⃣ Import Schema

**Via Command Line** (Recommended):
```bash
mysql -u root -p ecommerce2 < ecommerce2.sql
```

**Via phpMyAdmin**:
1. Select database: `ecommerce2`
2. Tab "Import"
3. Choose file: `ecommerce2.sql`
4. Click "Go"

### 3️⃣ Verifikasi Database

```bash
# Login ke MySQL
mysql -u root -p

# Verifikasi
USE ecommerce2;
SHOW TABLES;

# Expected output: 
# +--------------------+
# | Tables_in_ecommerce2 |
# +--------------------+
# | cart               |
# | categories         |
# | orders             |
# | order_items        |
# | products           |
# | users              |
# | user_info          |
# +--------------------+
```

### 4️⃣ Default Admin Account

```
Email:    admin@shop.com
Password: admin
(Dari SQL file - hash: 0192023a7bbd73250516f069df18b500)
```

⚠️ **PENTING**: Ubah password admin setelah login pertama!

### 5️⃣ Update Credentials di Code

Edit file: `db.php`

```php
<?php
$host = 'localhost';      // Host MySQL (biasanya localhost)
$user = 'root';           // Username MySQL
$password = '';           // Password MySQL (kosong jika tidak ada)
$database = 'ecommerce2'; // Nama database

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
```

---

## 💳 Konfigurasi Midtrans

### 1️⃣ Daftar Akun Midtrans

1. Buka: https://dashboard.midtrans.com
2. Klik "Sign Up"
3. Isi form registrasi
4. Verifikasi email
5. Login ke dashboard

### 2️⃣ Dapatkan API Keys

**Di Dashboard Midtrans**:

1. Settings → Configuration
2. Copy credentials untuk **Sandbox Environment**:
   - Server Key
   - Client Key

Contoh:
```
Server Key: Mid-server-HCbVcKmahJRTKy1pnC31kdSH
Client Key: Mid-client-MTEOLBDOBV54rCJL
```

### 3️⃣ Konfigurasi di Project

Edit file: `midtrans_config.php`

```php
<?php
require_once 'db.php';

// ===== MIDTRANS CONFIGURATION =====

// Environment: 'sandbox' atau 'production'
define('MIDTRANS_ENVIRONMENT', 'sandbox');  // Ubah ke 'production' saat go-live

// Sandbox Keys (dari dashboard Midtrans)
define('MIDTRANS_SERVER_KEY', 'Mid-server-HCbVcKmahJRTKy1pnC31kdSH');
define('MIDTRANS_CLIENT_KEY', 'Mid-client-MTEOLBDOBV54rCJL');

// Konfigurasi Snap
\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
\Midtrans\Config::$clientKey = MIDTRANS_CLIENT_KEY;
\Midtrans\Config::$isProduction = (MIDTRANS_ENVIRONMENT === 'production');
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Set Curl Timeout
\Midtrans\Config::$curlTimeout = 15;

// ===== HELPER FUNCTIONS =====

/**
 * Generate transaction data untuk Midtrans
 */
function generate_midtrans_transaction($orderData, $cartItems, $total) {
    // ... (lihat file midtrans_config.php untuk full code)
}

/**
 * Generate Snap token
 */
function generate_snap_token($transactionData) {
    // ... (lihat file midtrans_config.php untuk full code)
}

/**
 * Verify Midtrans webhook notification
 */
function verify_midtrans_transaction($notifBody) {
    // ... (lihat file midtrans_config.php untuk full code)
}
?>
```

### 4️⃣ Setup Notification URL (Webhook)

**Untuk menerima callback pembayaran**:

1. Di Dashboard Midtrans → Settings → Configuration
2. Tab "Notification URL"
3. Masukkan URL notification:
   ```
   https://yourdomain.com/api/midtrans_notification.php
   ```
4. Method: HTTP POST
5. Klik "Save"

### 5️⃣ Testing dengan Test Cards

**Midtrans Test Card** (Sandbox):

```
Kartu Kredit VISA (Success):
Nomor: 4811 1111 1111 1114
Bulan: 12
Tahun: 25
CVV: 123

Kartu Kredit VISA (Declined):
Nomor: 4911 1111 1111 1113
Bulan: 12
Tahun: 25
CVV: 123

Bank BCA (Success):
Internet Banking
Username: DEMO
Password: DEMOTEST
```

**Testing Flow**:
1. Buka website di browser
2. Login dengan akun test
3. Tambah produk ke cart
4. Checkout
5. Klik "Lanjutkan ke Pembayaran"
6. Gunakan test card di atas
7. Verifikasi order status berubah menjadi "paid"

### 6️⃣ Production Deployment

Ketika siap live, lakukan ini:

1. **Update API Keys** di `midtrans_config.php`:
   ```php
   define('MIDTRANS_ENVIRONMENT', 'production');
   define('MIDTRANS_SERVER_KEY', 'Mid-server-PRODUCTION-KEY-HERE');
   define('MIDTRANS_CLIENT_KEY', 'Mid-client-PRODUCTION-KEY-HERE');
   ```

2. **Update Notification URL** di Midtrans Dashboard (production)

3. **Enable HTTPS** (required untuk production):
   ```php
   \Midtrans\Config::$isProduction = true;
   ```

4. **Update Base URL** di `config.php`:
   ```php
   define('BASE_URL', 'https://yourdomain.com/ecommerce');
   ```

---

## ⚙️ Konfigurasi Environment

### File `config.php`

```php
<?php
// Base URL aplikasi
define('BASE_URL', 'http://localhost/ecommerce');
// Untuk production: define('BASE_URL', 'https://yourdomain.com/ecommerce');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Session config
ini_set('session.gc_maxlifetime', 3600);  // 1 jam
session_set_cookie_params(3600);

// Error reporting (disable di production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Untuk production: 
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', '/var/log/php-errors.log');
?>
```

### File `db.php`

```php
<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ecommerce2';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

// Helper functions
function escape($string) {
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

function clean($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
```

---

## 🧪 Testing & Deployment

### Pre-Launch Checklist

```
CONFIGURATION:
☐ Database berhasil terkoneksi
☐ Midtrans API keys sudah diinput
☐ BASE_URL sudah sesuai
☐ File permissions sudah diset (777 untuk upload folders)

ASSETS:
☐ Logo sudah di img/logo.png
☐ Banner sudah di img/banner1.jpg
☐ No-image placeholder di img/no-image.jpg

FUNCTIONALITY:
☐ User dapat register & login
☐ User dapat browse products
☐ User dapat add to cart
☐ User dapat checkout
☐ Snap payment modal terbuka
☐ Test payment berhasil (status menjadi "paid")
☐ Admin dapat login & manage products
☐ Admin dapat view orders

SECURITY:
☐ Admin default password sudah diubah
☐ HTTPS enabled (untuk production)
☐ Midtrans webhook signature verified
☐ SQL injection protection active
☐ XSS protection active
```

### Local Testing

```bash
# Start PHP built-in server (jika tidak pakai Laragon/XAMPP)
php -S localhost:8000

# Akses aplikasi
http://localhost:8000/
```

### Hosting Deployment

1. **Upload files** ke hosting:
   ```bash
   FTP / SFTP / GitHub
   ```

2. **Update database credentials** di `db.php`

3. **Update Midtrans keys** di `midtrans_config.php`

4. **Update BASE_URL** di `config.php`

5. **Set folder permissions**:
   ```bash
   chmod 755 product_images
   chmod 755 category_images
   chmod 755 img
   ```

6. **Import database** via phpMyAdmin atau command line

7. **Test akses** website

---

## 📚 Dokumentasi Tambahan

### File dokumentasi yang tersedia:

- `MIDTRANS_SETUP.md` - Setup detail Midtrans
- `MIDTRANS_TESTING_GUIDE.md` - Panduan testing pembayaran
- `MIDTRANS_QUICK_REFERENCE.md` - Quick reference Midtrans
- `MIDTRANS_IMPLEMENTATION_SUMMARY.md` - Ringkasan implementasi

### Struktur Database

**Tabel: users**
```sql
id (PK)          | Auto-increment
name             | Nama user
email            | Email (unique)
password         | Password hash (MD5/bcrypt)
is_admin         | 0 = customer, 1 = admin
created_at       | Timestamp
```

**Tabel: products**
```sql
id (PK)          | Auto-increment
product_cat      | Category ID (FK)
product_brand    | Brand name
name             | Product name
description      | Product description
price            | Price (decimal)
stock            | Stock quantity
image            | Image filename
featured         | 0 = normal, 1 = featured
created_at       | Timestamp
```

**Tabel: orders**
```sql
id (PK)          | Auto-increment
user_id (FK)     | User ID
total            | Total price (decimal)
status           | enum: pending, paid, shipped, completed, cancelled
shipping_address | Shipping address
shipping_phone   | Phone number
shipping_city    | City
shipping_postal_code | Postal code
notes            | Order notes
created_at       | Timestamp
updated_at       | Auto-updated timestamp
```

---

## ❓ Troubleshooting

### Masalah: "Koneksi database gagal"

**Solusi**:
1. Verifikasi MySQL sudah running
2. Check username & password di `db.php`
3. Verifikasi database `ecommerce2` sudah di-create
4. Cek file `db.php` permissions

### Masalah: "Midtrans API error"

**Solusi**:
1. Verifikasi server key & client key di `midtrans_config.php`
2. Pastikan internet connection aktif
3. Check Midtrans dashboard untuk status
4. Verifikasi environment setting (sandbox/production)

### Masalah: "Image upload tidak bekerja"

**Solusi**:
1. Set folder permissions: `chmod 777 product_images`
2. Verifikasi folder sudah di-create
3. Check PHP file upload limit di `php.ini`:
   ```ini
   upload_max_filesize = 50M
   post_max_size = 50M
   ```

### Masalah: "Session timeout terlalu cepat"

**Solusi**:
1. Update session config di `config.php`:
   ```php
   ini_set('session.gc_maxlifetime', 86400);  // 24 jam
   session_set_cookie_params(86400);
   ```

### Masalah: "Webhook notification tidak bekerja"

**Solusi**:
1. Verifikasi notification URL di Midtrans dashboard
2. Pastikan URL accessible dari internet (test dengan curl)
3. Check file permissions `api/midtrans_notification.php`
4. Verify server key match dengan signature verification

---

## 📞 Support & Resources

### Dokumentasi Resmi:
- Midtrans Documentation: https://docs.midtrans.com
- PHP Documentation: https://www.php.net/manual
- MySQL Documentation: https://dev.mysql.com/doc

### Komunitas:
- Midtrans Forum: https://forum.midtrans.com
- Stack Overflow: https://stackoverflow.com
- GitHub Issues: https://github.com/

---

## 📝 Changelog

**Version 1.0.0** - November 2025
- Initial release
- Midtrans payment integration
- Admin dashboard
- User authentication
- Responsive design
- Webhook notification support

---

## 📄 License

Proprietary. Semua hak cipta dilindungi.

---

## 👨‍💻 Author

Developed by: [Your Name/Company]
Last Updated: November 29, 2025

---

**✅ Siap untuk production! Happy coding! 🚀**
