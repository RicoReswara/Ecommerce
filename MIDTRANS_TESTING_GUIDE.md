# 🧪 MIDTRANS TESTING GUIDE

## 📋 Pre-Testing Checklist

- [ ] Database sudah diupdate dengan schema baru
- [ ] File midtrans_config.php ada di root
- [ ] File api/midtrans_notification.php ada
- [ ] checkout.php dan order_success.php sudah diupdate
- [ ] Web server bisa akses internet (untuk Midtrans API)
- [ ] PHP error logging enabled

---

## 🧪 Test Case 1: Successful Payment

### Setup
1. Pastikan Anda sudah login
2. Tambah beberapa product ke cart

### Execution
```
1. Buka: http://localhost/ecommerce/checkout.php
2. Isi form checkout dengan data:
   - Nama Penerima: Nama Anda
   - Alamat: Jl. Test No. 123, Jakarta
   - Kota: Jakarta
   - Kode Pos: 12345
   - Nomor Telepon: 081234567890
   - Catatan: Barang mudah pecah (optional)
   
3. Klik "Lanjutkan ke Pembayaran"
4. Tunggu modal Midtrans terbuka
5. Pilih "Credit Card" sebagai payment method
6. Input kartu test:
   - Card: 4811 1111 1111 1114
   - Expiry: 12/25
   - CVV: 123
7. Klik Pay
8. Tunggu processing...
```

### Expected Result
```
✅ Modal tutup otomatis
✅ Redirect ke order_success.php
✅ Status: "Pembayaran Berhasil" (hijau)
✅ Order items ditampilkan
✅ Total pembayaran benar
✅ Alamat pengiriman benar
```

### Verification
1. **Database:** 
   ```sql
   SELECT * FROM orders WHERE id = (SELECT MAX(id) FROM orders);
   -- Cek: order_status = 'confirmed', payment_status = 'paid'
   ```

2. **Midtrans Dashboard:**
   - Buka https://app.sandbox.midtrans.com/
   - Tab "Monitor" → cari order
   - Status: Settlement

---

## ❌ Test Case 2: Failed Payment

### Setup
Sama seperti Test Case 1

### Execution
```
1. Buka checkout.php
2. Isi form checkout
3. Click "Lanjutkan ke Pembayaran"
4. Modal Midtrans terbuka
5. Pilih "Credit Card"
6. Input kartu FAILED:
   - Card: 4111 1111 1111 1112
   - Expiry: 12/25
   - CVV: 123
7. Klik Pay
```

### Expected Result
```
✅ Payment denied/failed
✅ Tampil pesan error di modal
✅ Redirect ke checkout.php
✅ Order masih ada di database dengan status pending
```

### Verification
```sql
SELECT * FROM orders ORDER BY id DESC LIMIT 1;
-- Cek: payment_status = 'failed' atau order_status = 'cancelled'
```

---

## 🔒 Test Case 3: 3D Secure/Challenge

### Setup
Sama seperti Test Case 1

### Execution
```
1. Buka checkout.php
2. Isi form checkout
3. Click "Lanjutkan ke Pembayaran"
4. Modal Midtrans terbuka
5. Pilih "Credit Card"
6. Input kartu 3DS:
   - Card: 5200 3333 3333 3010
   - Expiry: 12/25
   - CVV: 123
7. Klik Pay
8. Tunggu 3D Secure challenge screen
9. Input OTP yang muncul
```

### Expected Result
```
✅ 3D Secure verification page tampil
✅ Setelah verify, redirect ke order_success.php
✅ Order status: confirmed
```

---

## 📱 Test Case 4: Mobile Responsive

### Setup
- Buka browser developer tools (F12)
- Set ke "Mobile" view
- Test dengan berbagai ukuran:
  - iPhone SE (375x667)
  - iPhone 12 (390x844)
  - Android (412x915)

### Execution
```
1. Buka checkout.php di mobile view
2. Scroll dan lihat layout
3. Fill form
4. Submit
5. Modal terbuka
6. Lakukan pembayaran
7. Check responsiveness di order_success.php
```

### Checklist
- [ ] Form fields readable dan tidak overlap
- [ ] Buttons clickable (ukuran minimal 44x44px)
- [ ] Modal Midtrans responsive
- [ ] Summary card tidak menyempit
- [ ] Text readable (font size OK)
- [ ] No horizontal scroll

---

## 🔄 Test Case 5: Webhook Notification

### Setup
- Pembayaran sudah berhasil
- Webhook sudah dikirim oleh Midtrans

### Monitoring
```
1. Buka Midtrans Dashboard (sandbox)
2. Tab "Monitor"
3. Cari order Anda (gunakan Order ID)
4. Lihat status pembayaran
5. Di column "Notification", cek apakah sudah terkirim
```

### Backend Verification
```
1. Check PHP error log:
   tail -f /var/log/php-fpm.log
   atau
   tail -f /var/www/ecommerce/error.log

2. Cari line:
   "Midtrans Notification: {...}"
   "Order #X updated: Status=confirmed"

3. Database verify:
   SELECT * FROM orders WHERE id = X;
   -- Cek payment_status = 'paid' & order_status = 'confirmed'
```

---

## 🛒 Test Case 6: Full User Flow

### Scenario: Complete E-Commerce Purchase

```
1. LOGIN
   - Buka login.php
   - Login dengan akun test
   
2. BROWSE PRODUCTS
   - Buka products.php
   - Filter/search produk
   - View product detail
   
3. ADD TO CART
   - Click "Tambah ke Keranjang"
   - Lihat cart count bertambah
   - Buka cart.php
   - Verify items ada
   
4. CHECKOUT
   - Click "Checkout"
   - Fill form lengkap
   - Submit
   
5. PAYMENT
   - Modal terbuka
   - Select payment method
   - Input kartu test
   - Process payment
   
6. SUCCESS PAGE
   - Verify order details
   - Check summary correct
   
7. VERIFICATION
   - Check database
   - Check Midtrans dashboard
   - Check error logs
```

---

## 📊 Test Case 7: Database Integrity

### Before Checkout
```sql
SELECT * FROM cart WHERE user_id = 1;
-- Cek items ada di cart
```

### After Successful Payment
```sql
-- Cek order dibuat
SELECT * FROM orders WHERE user_id = 1 ORDER BY id DESC LIMIT 1;

-- Cek order items
SELECT * FROM order_items WHERE order_id = (SELECT MAX(id) FROM orders);

-- Cek cart dikosongkan
SELECT * FROM cart WHERE user_id = 1;
-- Seharusnya kosong (0 rows)

-- Cek payment columns terisi
SELECT id, order_status, payment_status, transaction_id, payment_method 
FROM orders 
WHERE user_id = 1 
ORDER BY id DESC LIMIT 1;
```

---

## 🔍 Test Case 8: Error Handling

### Test: Invalid Order ID
```
1. Buka: http://localhost/ecommerce/order_success.php?order_id=99999
2. Expected: Error message "Pesanan tidak ditemukan"
3. Redirect ke index.php
```

### Test: Order Dari User Lain
```
1. Create 2 user account (user1, user2)
2. User1 buat order (order_id = 5)
3. Logout, login as user2
4. Buka: http://localhost/ecommerce/order_success.php?order_id=5
5. Expected: Error message "Pesanan tidak ditemukan"
```

### Test: Unauthorized Access
```
1. Logout semua session
2. Buka checkout.php
3. Expected: Redirect ke login.php
```

---

## 📋 Test Data Reference

### Valid Test Cards

| Type | Card Number | Exp | CVV | Description |
|------|-------------|-----|-----|-------------|
| Success | 4811 1111 1111 1114 | 12/25 | 123 | Pembayaran berhasil |
| Failed | 4111 1111 1111 1112 | 12/25 | 123 | Pembayaran ditolak |
| 3DS | 5200 3333 3333 3010 | 12/25 | 123 | Challenge 3DS |

### Sample Form Data
```
Nama Penerima: John Doe
Alamat: Jl. Merdeka No. 123, RT 01/RW 05
Kota: Jakarta Pusat
Kode Pos: 10120
Telepon: 081234567890
Catatan: Hati-hati, barang mudah pecah
```

---

## ✅ Testing Checklist

### Functional Testing
- [ ] Checkout form validation works
- [ ] Snap token generated successfully
- [ ] Modal opens automatically
- [ ] Payment with valid card succeeds
- [ ] Payment with invalid card fails
- [ ] Redirect to success page works
- [ ] Order details displayed correctly

### Database Testing
- [ ] Order created with correct data
- [ ] Order items saved correctly
- [ ] Payment columns updated
- [ ] Cart cleared after payment
- [ ] Webhook updates order status

### Integration Testing
- [ ] Midtrans API connection works
- [ ] Webhook notification received
- [ ] Signature verification works
- [ ] Status mapping correct
- [ ] Error handling works

### UI/UX Testing
- [ ] Desktop layout looks good
- [ ] Mobile layout responsive
- [ ] Forms accessible
- [ ] Buttons clickable
- [ ] Error messages clear

### Security Testing
- [ ] API keys not exposed in frontend
- [ ] Webhook signature verified
- [ ] User authorization checked
- [ ] SQL injection prevented
- [ ] XSS prevention working

---

## 🐛 Common Issues & Solutions

### Issue: Snap token returns null
```
Solution:
1. Check PHP error_log for exception
2. Verify API keys correct
3. Check Midtrans PHP library loaded
4. Verify network connection to Midtrans
```

### Issue: Modal tidak muncul
```
Solution:
1. Check browser console (F12)
2. Verify Midtrans Snap script loaded
3. Check snap_token in database
4. Check session variables set correctly
```

### Issue: Webhook tidak masuk
```
Solution:
1. Check Midtrans Monitor tab
2. Verify Notification URL registered
3. Check firewall rules
4. Check PHP error log for exceptions
```

### Issue: Order status tidak update
```
Solution:
1. Check webhook received in Monitor
2. Check signature verification pass
3. Check database update query
4. Check MySQL user permissions
```

---

## 📝 Log What You Should See

### Success Scenario Logs:

**PHP Error Log:**
```
[28-Nov-2025 10:30:45] Midtrans Notification: {"order_id":"ORDER-5-1732772445",...}
[28-Nov-2025 10:30:45] Order #5 updated: Status=confirmed, Payment=paid
```

**Midtrans Monitor:**
- Transaction Status: Settlement
- Notification: Sent ✓
- Amount: Rp 1,234,567.00

**Database:**
```
| id | order_status | payment_status | transaction_id | payment_method |
| 5  | confirmed    | paid           | ORDER-5-...    | credit_card    |
```

---

## 🎓 Next Steps After Testing

1. **Fix any bugs found**
   - Update code
   - Test again
   - Commit to git

2. **Performance testing**
   - Load test checkout page
   - Monitor server response time
   - Optimize if needed

3. **Security audit**
   - Code review
   - Penetration testing
   - Security headers check

4. **Production migration**
   - Get production API keys
   - Update configuration
   - Final testing with real payment
   - Deploy to production

---

## 📞 Support Resources

- **Midtrans Test Cards:** https://docs.midtrans.com/en/technical-reference/sandbox-credentials
- **Sandbox Dashboard:** https://app.sandbox.midtrans.com/
- **Troubleshooting:** https://support.midtrans.com/
- **API Docs:** https://api-docs.midtrans.com/

---

**Testing Version:** 1.0  
**Last Updated:** November 28, 2025  
**Status:** Ready for Testing ✅
