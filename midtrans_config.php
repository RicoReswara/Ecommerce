<?php
/**
 * Midtrans Configuration
 * Konfigurasi untuk integrasi Midtrans Payment Gateway
 */

// Environment: 'sandbox' atau 'production'
define('MIDTRANS_ENVIRONMENT', 'sandbox');

// Server Key (gunakan untuk backend)
define('MIDTRANS_SERVER_KEY', 'Mid-server-HCbVcKmahJRTKy1pnC31kdSH');

// Client Key (gunakan untuk frontend)
define('MIDTRANS_CLIENT_KEY', 'Mid-client-MTEOLBDOBV54rCJL');

// Merchant ID
define('MIDTRANS_MERCHANT_ID', 'G123456');

// Base URL untuk redirect setelah pembayaran
define('MIDTRANS_REDIRECT_URL', BASE_URL . '/order_success.php');

// Notification URL untuk webhook dari Midtrans
define('MIDTRANS_NOTIFICATION_URL', BASE_URL . '/api/midtrans_notification.php');

// Set konfigurasi Midtrans
require_once __DIR__ . '/vendor/midtrans/midtrans-php/Midtrans.php';

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
\Midtrans\Config::$clientKey = MIDTRANS_CLIENT_KEY;
\Midtrans\Config::$isProduction = (MIDTRANS_ENVIRONMENT === 'production');
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

/**
 * Generate Midtrans Transaction Data
 * 
 * @param array $orderData Data pesanan dari database
 * @param array $items Daftar item dalam keranjang
 * @param float $totalAmount Total amount pembayaran
 * @return array Array transaksi untuk Midtrans Snap
 */
function generate_midtrans_transaction($orderData, $items, $totalAmount) {
    $transactionDetails = array(
        'order_id' => 'ORDER-' . $orderData['id'] . '-' . time(),
        'gross_amount' => intval($totalAmount),
    );

    $customerDetails = array(
        'first_name' => explode(' ', $orderData['customer_name'])[0],
        'last_name' => implode(' ', array_slice(explode(' ', $orderData['customer_name']), 1)),
        'email' => $orderData['customer_email'],
        'phone' => $orderData['phone'],
    );

    $itemDetails = array();
    foreach ($items as $item) {
        $itemDetails[] = array(
            'id' => 'ITEM-' . $item['product_id'],
            'price' => intval($item['price']),
            'quantity' => intval($item['quantity']),
            'name' => $item['name'],
        );
    }

    $transaction = array(
        'transaction_details' => $transactionDetails,
        'customer_details' => $customerDetails,
        'item_details' => $itemDetails,
    );

    return $transaction;
}

/**
 * Generate Midtrans Snap Token
 * 
 * @param array $transaction Data transaksi
 * @return string Snap token untuk pembayaran
 */
function generate_snap_token($transaction) {
    try {
        $snapToken = \Midtrans\Snap::getSnapToken($transaction);
        return $snapToken;
    } catch (Exception $e) {
        error_log('Midtrans Error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Verify Midtrans Transaction
 * 
 * @param string $orderId Order ID
 * @return array Transaction status dari Midtrans
 */
function verify_midtrans_transaction($orderId) {
    try {
        $status = \Midtrans\Transaction::status($orderId);
        return $status;
    } catch (Exception $e) {
        error_log('Midtrans Verification Error: ' . $e->getMessage());
        return null;
    }
}
?>
