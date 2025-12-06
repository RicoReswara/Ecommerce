# 🔧 Panduan Konfigurasi Midtrans - FIX

## ❌ Masalah yang Anda Alami

Setelah halaman Snap muncul dan Anda cek status, tidak ada perubahan karena:
1. **Notification URL salah** - File `payment_process.php` tidak ada
2. **URL tidak lengkap** - Harus include `/api/` di path
3. **Callback tidak dikonfigurasi dengan benar**

---

## ✅ Konfigurasi Dashboard Midtrans yang BENAR

### 1️⃣ Login ke Dashboard Midtrans
- Sandbox: https://dashboard.sandbox.midtrans.com
- Production: https://dashboard.midtrans.com

### 2️⃣ Masuk ke Settings → Configuration

### 3️⃣ Set URL dengan Format Berikut:

#### **Payment Notification URL** (WAJIB!)
```
https://eshop.page.gd/api/midtrans_notification.php
```
⚠️ **PENTING:** 
- Harus ada `/api/` di path
- File ini menerima webhook dari Midtrans untuk update status pembayaran
- **BUKAN** `payment_process.php`

#### **Finish Redirect URL** (Opsional)
```
https://eshop.page.gd/order_success.php
```
📌 URL ini sudah dikonfigurasi di dalam kode (callback `onSuccess`)

#### **Unfinish Redirect URL** (Opsional)
```
https://eshop.page.gd/cart.php
```

#### **Error Redirect URL** (Opsional)
```
https://eshop.page.gd/cart.php
```

---

## 🔍 Cara Kerja Sistem Payment

### Flow Pembayaran:

```
1. User klik "Bayar" → Snap Modal muncul
2. User bayar di Snap Modal
3. Midtrans kirim notifikasi ke: /api/midtrans_notification.php
4. File notification update status order di database
5. User redirect ke: order_success.php
6. User lihat status pembayaran yang sudah terupdate
```

### File-File Penting:

| File | Fungsi | URL |
|------|--------|-----|
| `checkout.php` | Membuat order & generate snap token | Manual access |
| `api/midtrans_notification.php` | Menerima webhook dari Midtrans | **Harus di set di dashboard** |
| `order_success.php` | Halaman konfirmasi setelah bayar | Redirect otomatis |
| `midtrans_config.php` | Konfigurasi Midtrans | Backend only |

---

## 🧪 Cara Testing

### 1. Test di Sandbox

1. **Cek Notification URL sudah benar:**
   - Buka Dashboard Midtrans → Settings → Configuration
   - Pastikan Notification URL: `https://eshop.page.gd/api/midtrans_notification.php`
   - Klik **Save**

2. **Lakukan Test Payment:**
   - Buat order baru
   - Snap modal akan muncul
   - Gunakan test card (Sandbox):
     - Card Number: `4811 1111 1111 1114`
     - Exp: `01/25`
     - CVV: `123`
   
3. **Cek Status Order:**
   - Setelah bayar berhasil → akan redirect ke `order_success.php`
   - Status order harus berubah dari `pending` → `paid`
   - Cek di menu "Pesanan Saya"

### 2. Debugging

#### Cek Log Midtrans Notification:
```bash
# Buka file log PHP Anda
tail -f C:\laragon\tmp\php_errors.log
```

Atau cek di Dashboard Midtrans → Transactions → Klik transaksi → Tab "Notifications"

#### Jika Status TIDAK Berubah:

**A. Cek Notification URL:**
```
Dashboard Midtrans → Settings → Configuration → Payment Notification URL
Harus: https://eshop.page.gd/api/midtrans_notification.php
```

**B. Test Notification URL Manual:**
```bash
curl -X POST https://eshop.page.gd/api/midtrans_notification.php \
  -H "Content-Type: application/json" \
  -d '{"test": "test"}'
```
Harus return response, bukan error 404

**C. Cek Database:**
```sql
SELECT id, status, created_at, updated_at FROM orders ORDER BY id DESC LIMIT 5;
```

**D. Cek File Log:**
File `api/midtrans_notification.php` akan log semua request ke PHP error log

---

## 🔐 Environment Variables

Pastikan di `midtrans_config.php` sudah benar:

```php
// Sandbox
define('MIDTRANS_ENVIRONMENT', 'sandbox');
define('MIDTRANS_SERVER_KEY', 'YOUR_SERVER_KEY');
define('MIDTRANS_CLIENT_KEY', 'YOUR_CLIENT_KEY');
```

---

## 📱 Testing dengan Berbagai Metode Pembayaran

### Sandbox Test Credentials:

| Payment Method | Card Number | Result |
|---------------|-------------|--------|
| Credit Card (Success) | `4811 1111 1111 1114` | Success |
| Credit Card (Failure) | `4911 1111 1111 1113` | Failed |
| GoPay | Use test phone: `81234567890` | Success |
| Alfamart | Will generate payment code | Pending |

---

## ⚠️ Checklist Sebelum Production

- [ ] Ganti `MIDTRANS_ENVIRONMENT` ke `'production'`
- [ ] Ganti Server Key & Client Key ke production key
- [ ] Update domain di Dashboard Midtrans production
- [ ] Test sekali lagi dengan production sandbox
- [ ] Set notification URL production: `https://yourdomain.com/api/midtrans_notification.php`
- [ ] Pastikan HTTPS aktif (SSL certificate valid)

---

## 📞 Support

Jika masih ada masalah:
1. Cek log di `api/midtrans_notification.php`
2. Cek Dashboard Midtrans → Transactions → Notifications tab
3. Pastikan server bisa diakses dari internet (tidak localhost)
4. Test manual POST ke notification URL

**Midtrans Documentation:** https://docs.midtrans.com
