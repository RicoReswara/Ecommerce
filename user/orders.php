<?php
$page_title = "Pesanan Saya";
require_once '../db.php';
require_once '../config.php';
require_once '../midtrans_config.php';

// Require login
require_login();

$user_id = $_SESSION['user_id'];

// Get user orders
$orders_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$orders = mysqli_query($conn, $orders_query);

include '../header.php';
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-bag"></i> Pesanan Saya</h2>

    <?php if (mysqli_num_rows($orders) > 0): ?>
        <div class="row g-4">
            <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                <?php
                // Get order items
                $items_query = "SELECT * FROM order_items WHERE order_id = {$order['id']}";
                $items = mysqli_query($conn, $items_query);
                $item_count = mysqli_num_rows($items);
                ?>

                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-0">
                                        <strong>Order #<?php echo $order['id']; ?></strong>
                                        <span class="text-muted">• <?php echo date('d F Y, H:i', strtotime($order['created_at'])); ?></span>
                                    </h6>
                                </div>
                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                    <?php
                                    $badge_class = '';
                                    switch ($order['status']) {
                                        case 'pending': $badge_class = 'bg-warning text-dark'; break;
                                        case 'paid': $badge_class = 'bg-info'; break;
                                        case 'shipped': $badge_class = 'bg-primary'; break;
                                        case 'completed': $badge_class = 'bg-success'; break;
                                        case 'cancelled': $badge_class = 'bg-danger'; break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="fw-bold mb-3">Item Pesanan (<?php echo $item_count; ?>)</h6>
                                    <div class="list-group list-group-flush">
                                        <?php while ($item = mysqli_fetch_assoc($items)): ?>
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong><?php echo clean($item['product_name']); ?></strong>
                                                        <p class="mb-0 text-muted small">
                                                            <?php echo $item['quantity']; ?>x <?php echo format_rupiah($item['price']); ?>
                                                        </p>
                                                    </div>
                                                    <div class="text-end">
                                                        <strong><?php echo format_rupiah($item['price'] * $item['quantity']); ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3">Ringkasan</h6>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Total</span>
                                                <strong class="text-primary"><?php echo format_rupiah($order['total']); ?></strong>
                                            </div>
                                            <?php if ($order['status'] == 'pending' && !empty($order['snap_token'])): ?>
                                            <div class="d-grid mt-3">
                                                <button class="btn btn-warning btn-pay-now" data-snap-token="<?php echo $order['snap_token']; ?>">
                                                    <i class="bi bi-credit-card"></i> Bayar Sekarang
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                            <hr>
                                            <h6 class="fw-bold mb-2">Pengiriman</h6>
                                            <p class="small mb-1"><?php echo nl2br(clean($order['shipping_address'])); ?></p>
                                            <p class="small mb-1"><?php echo clean($order['shipping_city']); ?> <?php echo clean($order['shipping_postal_code']); ?></p>
                                            <p class="small mb-0"><i class="bi bi-telephone"></i> <?php echo clean($order['shipping_phone']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-bag-x text-muted" style="font-size: 5rem;"></i>
                <h4 class="mt-4 mb-3">Belum Ada Pesanan</h4>
                <p class="text-muted mb-4">Anda belum memiliki pesanan. Yuk mulai berbelanja!</p>
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop"></i> Mulai Belanja
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Load Midtrans Snap JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>
<script>
window.addEventListener('load', function() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-pay-now')) {
            e.preventDefault();
            const btn = e.target.closest('.btn-pay-now');
            const snapToken = btn.getAttribute('data-snap-token');
            
            if (typeof snap === 'undefined') {
                alert('Payment gateway sedang dimuat. Silakan coba lagi.');
                return;
            }
            
            if (!snapToken) {
                alert('Token pembayaran tidak ditemukan.');
                return;
            }
            
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    window.location.reload();
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    window.location.reload();
                },
                onError: function(result) {
                    console.error('Payment error:', result);
                    alert('Pembayaran gagal! Silakan coba lagi.');
                },
                onClose: function() {
                    console.log('Payment modal closed');
                }
            });
        }
    });
});
</script>

<?php include '../footer.php'; ?>
