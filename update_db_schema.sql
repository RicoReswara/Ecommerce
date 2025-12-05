-- Update Orders Table Schema untuk Midtrans Integration
-- Database: ecommerce2

-- Tambahkan kolom untuk order status
ALTER TABLE orders ADD COLUMN order_status VARCHAR(50) DEFAULT 'pending';
ALTER TABLE orders ADD COLUMN payment_status VARCHAR(50) DEFAULT 'pending';
ALTER TABLE orders ADD COLUMN transaction_id VARCHAR(255);
ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50);
ALTER TABLE orders ADD COLUMN snap_token VARCHAR(255);

-- Buat index untuk query lebih cepat
ALTER TABLE orders ADD INDEX idx_order_status (order_status);
ALTER TABLE orders ADD INDEX idx_payment_status (payment_status);
ALTER TABLE orders ADD INDEX idx_transaction_id (transaction_id);
