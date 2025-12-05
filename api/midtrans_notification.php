<?php
/**
 * Midtrans Notification Handler
 * File ini menangani webhook/callback dari Midtrans
 */

require_once '../db.php';
require_once '../config.php';
require_once '../midtrans_config.php';

// Log semua request untuk debugging
$inputData = file_get_contents('php://input');
error_log('Midtrans Notification: ' . $inputData);

// Validasi signature dari Midtrans
$data = json_decode($inputData, true);

if (!isset($data['signature_key'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
    exit;
}

// Generate signature untuk verifikasi
$orderId = $data['order_id'] ?? '';
$statusCode = $data['status_code'] ?? '';
$grossAmount = $data['gross_amount'] ?? '';
$serverKey = MIDTRANS_SERVER_KEY;

$signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

if ($signature !== $data['signature_key']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
    exit;
}

// Proses notification
try {
    $transactionStatus = $data['transaction_status'] ?? '';
    $paymentType = $data['payment_type'] ?? '';
    $fraudStatus = $data['fraud_status'] ?? '';
    
    // Extract order ID
    preg_match('/ORDER-(\d+)-/', $orderId, $matches);
    $orderIdDb = $matches[1] ?? 0;

    if ($orderIdDb == 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Order not found']);
        exit;
    }

    // Update status order berdasarkan transaction status dari Midtrans
    // Status enum di database: pending, paid, shipped, completed, cancelled
    $orderStatus = 'pending';

    if ($transactionStatus == 'capture') {
        if ($fraudStatus == 'accept') {
            $orderStatus = 'paid';
        }
    } else if ($transactionStatus == 'settlement') {
        $orderStatus = 'paid';
    } else if ($transactionStatus == 'pending') {
        $orderStatus = 'pending';
    } else if ($transactionStatus == 'deny') {
        $orderStatus = 'cancelled';
    } else if ($transactionStatus == 'cancel' || $transactionStatus == 'expire') {
        $orderStatus = 'cancelled';
    }

    // Update order di database
    $updateQuery = "UPDATE orders SET 
                    status = '$orderStatus',
                    updated_at = NOW()
                    WHERE id = $orderIdDb";

    if (mysqli_query($conn, $updateQuery)) {
        // Log transaksi
        error_log("Order #$orderIdDb updated: Status=$orderStatus from Midtrans transaction: $transactionStatus");
        
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Order updated']);
    } else {
        error_log("Database update error: " . mysqli_error($conn));
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }

} catch (Exception $e) {
    error_log('Notification Handler Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
