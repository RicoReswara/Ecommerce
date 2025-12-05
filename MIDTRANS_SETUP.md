# Setup Midtrans Payment Gateway - E-Commerce

Panduan lengkap untuk mengintegrasikan Midtrans Payment Gateway pada sistem e-commerce Anda.

## 📋 Daftar Isi
1. [Informasi API Keys](#informasi-api-keys)
2. [Persiapan Database](#persiapan-database)
3. [Konfigurasi File](#konfigurasi-file)
4. [Cara Kerja Payment Flow](#cara-kerja-payment-flow)
5. [Testing](#testing)
6. [Production Setup](#production-setup)
7. [Troubleshooting](#troubleshooting)

---

## 🔑 Informasi API Keys

### Sandbox Keys (Testing)
```
Client Key:    Mid-client-MTEOLBDOBV54rCJL
Server Key:    Mid-server-HCbVcKmahJRTKy1pnC31kdSH
```

### Environment Setting
- **Current Environment**: Sandbox (untuk testing)
- **Merchant ID**: G123456

---

## 🗄️ Persiapan Database

### 1. Update Table `orders` (Jika Belum Ada)

Pastikan tabel `orders` memiliki kolom-kolom berikut:

```sql
ALTER TABLE orders ADD COLUMN IF NOT EXISTS transaction_id VARCHAR(255);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS snap_token VARCHAR(255);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS order_status VARCHAR(50) DEFAULT 'pending';
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_status VARCHAR(50) DEFAULT 'pending';
ALTER TABLE orders CHANGE COLUMN status order_status VARCHAR(50) DEFAULT 'pending';
ALTER TABLE orders ADD INDEX idx_transaction_id (transaction_id);
```

### 2. Status Order yang Digunakan

**order_status:**
- `pending` - Pesanan baru, menunggu pembayaran
- `confirmed` - Pembayaran berhasil, siap dikirim
- `shipped` - Sedang dikirim
- `delivered` - Sudah diterima
- `cancelled` - Dibatalkan
- `refunded` - Dikembalikan

**payment_status:**
- `pending` - Menunggu pembayaran
- `paid` - Pembayaran berhasil
- `failed` - Pembayaran gagal
- `cancelled` - Pembayaran dibatalkan
- `refunded` - Pengembalian dana

---

## ⚙️ Konfigurasi File

### File-file yang Dibuat/Dimodifikasi:

#### 1. **midtrans_config.php** (Baru)
Lokasi: `/midtrans_config.php`

File ini berisi:
- Konfigurasi API Keys
- Fungsi `generate_midtrans_transaction()` - Generate data transaksi
- Fungsi `generate_snap_token()` - Generate Snap token untuk pembayaran
- Fungsi `verify_midtrans_transaction()` - Verifikasi status transaksi

**Cara Menggunakan:**
```php
require_once 'midtrans_config.php';

// Generate transaction data
$orderData = [
    'id' => $order_id,
    'customer_name' => $customer_name,
    'customer_email' => $customer_email,
    'phone' => $phone_number,
];

$transaction = generate_midtrans_transaction($orderData, $cart_items, $total);
$snapToken = generate_snap_token($transaction);
```

#### 2. **api/midtrans_notification.php** (Baru)
Lokasi: `/api/midtrans_notification.php`

File ini menangani:
- Webhook/callback dari Midtrans
- Verifikasi signature untuk keamanan
- Update status order di database

**Notification URL (daftarkan di dashboard Midtrans):**
```
https://yourdomain.com/api/midtrans_notification.php
```

#### 3. **checkout.php** (Dimodifikasi)
Lokasi: `/checkout.php`

Perubahan utama:
- Integrasi Midtrans Snap
- Auto-open payment modal setelah order dibuat
- Styling modern seperti Tokopedia
- Mobile-friendly responsive design
- Tambahan field: nama penerima, opsi pengiriman

**Flow Checkout Baru:**
1. Customer mengisi form checkout (alamat, dll)
2. Order dibuat di database (status: pending)
3. Snap token dihasilkan dari Midtrans
4. Modal Midtrans otomatis terbuka
5. Customer melakukan pembayaran
6. Setelah pembayaran, redirect ke order_success.php
7. Webhook Midtrans update status order di database

---

## 🔄 Cara Kerja Payment Flow

### 1. **Order Creation Flow**
```
Customer fills checkout form
        ↓
Create order (status: pending, payment_status: pending)
        ↓
Generate Midtrans transaction data
        ↓
Get Snap token from Midtrans
        ↓
Save snap_token to order
        ↓
Clear shopping cart
        ↓
Show Midtrans payment modal
```

### 2. **Payment Processing Flow**
```
Customer enters payment details in Midtrans modal
        ↓
Midtrans processes payment
        ↓
On Success:
  ├─ Redirect to order_success.php
  ├─ Show order confirmation
  └─ Webhook updates order status
  
On Failure:
  └─ Show error message
  └─ Redirect back to checkout
```

### 3. **Webhook/Notification Flow**
```
Midtrans sends notification to /api/midtrans_notification.php
        ↓
Verify signature (for security)
        ↓
Parse transaction status
        ↓
Update order status in database:
  ├─ capture → confirmed
  ├─ settlement → confirmed (paid)
  ├─ pending → pending
  ├─ deny → cancelled (failed)
  ├─ cancel/expire → cancelled
  └─ refund → refunded
        ↓
Log transaction
```

---

## 🧪 Testing

### 1. **Test Credentials (Sandbox)**

Untuk testing pembayaran, gunakan kredensial fake yang disediakan Midtrans:

**Credit Card - Successful Payment:**
```
Card Number:    4811 1111 1111 1114
Expiry:         12/25
CVV:            123
```

**Credit Card - Failed Payment:**
```
Card Number:    4111 1111 1111 1112
Expiry:         12/25
CVV:            123
```

**Credit Card - Challenge (3D Secure):**
```
Card Number:    5200 3333 3333 3010
Expiry:         12/25
CVV:            123
```

### 2. **Testing Checklist**

- [ ] Order berhasil dibuat dengan status pending
- [ ] Snap token tergenerate dan modal terbuka
- [ ] Pembayaran berhasil (checkout success)
- [ ] Status order berubah menjadi confirmed setelah pembayaran
- [ ] Webhook notification diterima dengan baik
- [ ] Order success page menampilkan detail pesanan
- [ ] Mobile responsive berfungsi dengan baik
- [ ] Email notifikasi dikirim (jika ada)

### 3. **Debugging**

Lihat error log PHP untuk debugging:
```
/logs/error.log atau php error_log
```

atau enable query log MySQL untuk lihat update database.

---

## 🚀 Production Setup

### 1. **Update ke Production Environment**

Ubah di file `midtrans_config.php`:

```php
// Dari
define('MIDTRANS_ENVIRONMENT', 'sandbox');

// Menjadi
define('MIDTRANS_ENVIRONMENT', 'production');
```

### 2. **Update Production API Keys**

Dapatkan production API keys dari dashboard Midtrans:

```php
define('MIDTRANS_SERVER_KEY', 'YOUR_PRODUCTION_SERVER_KEY');
define('MIDTRANS_CLIENT_KEY', 'YOUR_PRODUCTION_CLIENT_KEY');
```

### 3. **Daftarkan Notification URL di Dashboard Midtrans**

1. Login ke dashboard Midtrans (https://dashboard.midtrans.com/)
2. Menu Settings → Configuration
3. Masukkan Notification URL (POST):
   ```
   https://yourdomain.com/api/midtrans_notification.php
   ```
4. Finish Redirect URL (untuk auto-redirect setelah pembayaran):
   ```
   https://yourdomain.com/order_success.php
   ```
5. Error Redirect URL:
   ```
   https://yourdomain.com/checkout.php
   ```

### 4. **Update Order Success Page**

File `order_success.php` perlu diupdate untuk:
- Ambil order ID dari query parameter
- Tampilkan detail pesanan dengan status pembayaran
- Option untuk lihat invoice/nota

### 5. **Setup Stock Management**

**Penting:** Stock hanya dikurangi setelah pembayaran dikonfirmasi!

Tambahkan di `/api/midtrans_notification.php` setelah order status diubah ke confirmed:

```php
if ($orderStatus == 'confirmed' && $paymentStatus == 'paid') {
    // Get order items
    $items_query = "SELECT * FROM order_items WHERE order_id = $orderIdDb";
    $items = mysqli_query($conn, $items_query);
    
    // Update product stock
    while ($item = mysqli_fetch_assoc($items)) {
        $update_stock = "UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product_id']}";
        mysqli_query($conn, $update_stock);
    }
}
```

---

## 🐛 Troubleshooting

### Problem: Snap token tidak tergenerate

**Solusi:**
- Cek API keys sudah benar (di midtrans_config.php)
- Cek Midtrans library sudah ter-autoload dengan benar
- Cek error log untuk pesan error spesifik
- Pastikan file `/vendor/midtrans/midtrans-php/Midtrans.php` ada

### Problem: Webhook tidak diterima

**Solusi:**
- Cek Notification URL sudah terdaftar di dashboard Midtrans
- Cek firewall/security tidak memblokir request dari Midtrans
- Cek response code dari webhook (harus 200)
- Lihat log file untuk debugging

### Problem: Order status tidak update setelah pembayaran

**Solusi:**
- Cek webhook notification sudah dikirim oleh Midtrans (lihat di dashboard)
- Verifikasi signature di webhook handler (mungkin error key)
- Cek database permission untuk update query
- Debug dengan menambah error_log di notification handler

### Problem: Payment modal tidak muncul

**Solusi:**
- Cek session variable sudah set dengan benar
- Cek Midtrans Snap script sudah load (dari CDN)
- Cek browser console untuk JavaScript error
- Pastikan Client Key yang digunakan benar

### Problem: Redirect setelah pembayaran error

**Solusi:**
- Pastikan order_id ada di query parameter
- Cek order_success.php bisa menangani order_id dari URL
- Cek database sudah update order status
- Verifikasi BASE_URL setting sudah benar

---

## 📱 Mobile Optimization Tips

Checkout page sudah dioptimalkan untuk mobile dengan:
- Responsive grid layout
- Sticky summary card di mobile
- Touch-friendly buttons
- Optimized form inputs
- Midtrans modal sudah mobile-friendly

Testing di berbagai perangkat untuk memastikan semuanya berfungsi optimal.

---

## 📚 Resources

- [Midtrans Documentation](https://docs.midtrans.com/)
- [Snap Documentation](https://docs.midtrans.com/en/snap/overview)
- [API Reference](https://api-docs.midtrans.com/)
- [Sandbox Testing](https://docs.midtrans.com/en/technical-reference/sandbox-credentials)

---

## 📞 Support

Jika ada masalah atau pertanyaan:

1. **Midtrans Support**: https://support.midtrans.com/
2. **Check Status**: Dashboard → Monitor tab
3. **Logs**: Check error logs di server

---

**Last Updated**: November 28, 2025
**Version**: 1.0

