# 🚀 QUICK SETUP GUIDE

## ⚡ Setup dalam 5 Menit

### 1. Database
```bash
mysql -u root -p
CREATE DATABASE ecommerce2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce2;
SOURCE ecommerce2.sql;
```

### 2. Update Credentials
Edit `db.php`:
```php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ecommerce2';
```

### 3. Midtrans Config
Edit `midtrans_config.php`:
```php
define('MIDTRANS_SERVER_KEY', 'Mid-server-YOUR-KEY');
define('MIDTRANS_CLIENT_KEY', 'Mid-client-YOUR-KEY');
define('MIDTRANS_ENVIRONMENT', 'sandbox');  // atau 'production'
```

### 4. Dapatkan Midtrans Keys
1. Login: https://dashboard.midtrans.com
2. Settings → Configuration
3. Copy Server Key & Client Key dari Sandbox
4. Paste di `midtrans_config.php`

### 5. Folder Assets
- Logo: `img/logo.png` (200x60px)
- Banner: `img/banner1.jpg` (1920x600px)
- No Image: `img/no-image.jpg` (400x400px)

---

## 🔓 Default Login

**Admin:**
- Email: admin@shop.com
- Password: admin
- ⚠️ Ubah password setelah login!

**Test User:**
- Email: budi@example.com
- Password: 12345

---

## 🧪 Test Payment

**Test Card:**
```
Nomor: 4811 1111 1111 1114
Bulan: 12
Tahun: 25
CVV: 123
```

**Flow:**
1. Login sebagai customer
2. Tambah produk ke cart
3. Checkout
4. Gunakan test card di atas
5. Order status berubah → "paid" ✓

---

## 📁 File Structure

```
ecommerce/
├── img/
│   ├── logo.png          ← Ganti logo di sini
│   ├── banner1.jpg       ← Ganti banner di sini
│   └── no-image.jpg      ← Gambar default produk
├── product_images/       ← Auto-upload produk
├── category_images/      ← Auto-upload kategori
├── api/
│   └── midtrans_notification.php  ← Webhook
├── db.php                ← Edit credentials
├── config.php            ← Update BASE_URL
├── midtrans_config.php   ← Edit API keys
└── ecommerce2.sql        ← Database schema
```

---

## 🔧 Troubleshooting

| Error | Solusi |
|-------|--------|
| Koneksi database gagal | Check credentials di `db.php` |
| Midtrans error | Verifikasi API keys di `midtrans_config.php` |
| Image upload gagal | `chmod 777 product_images` |
| Session timeout | Increase di `config.php` |
| Webhook tidak jalan | Update notification URL di Midtrans dashboard |

---

## 📞 Support

- Docs: https://docs.midtrans.com
- Forum: https://forum.midtrans.com
- Lengkap: Lihat `README.md`

---

**Ready to go! Happy coding! 🎉**
