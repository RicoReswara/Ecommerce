<?php
$page_title = "Pesanan Berhasil";
require_once 'db.php';
require_once 'config.php';
require_once 'midtrans_config.php';
include 'header.php';

// Require login
require_login();

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Get order details
$order_query = "SELECT o.*,
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                FROM orders o
                WHERE o.id = $order_id AND o.user_id = $user_id
                LIMIT 1";
$order_result = mysqli_query($conn, $order_query);

if (!$order_result || mysqli_num_rows($order_result) == 0) {
    set_flash('danger', 'Pesanan tidak ditemukan.');
    redirect('index.php');
}

$order = mysqli_fetch_assoc($order_result);

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

// Determine payment status badge
$status = $order['status'] ?? 'pending';

$status_badge_class = 'bg-warning';
$status_text = 'Pending';

if ($status == 'paid') {
    $status_badge_class = 'bg-success';
    $status_text = 'Pembayaran Berhasil';
} elseif ($status == 'shipped') {
    $status_badge_class = 'bg-info';
    $status_text = 'Sedang Dikirim';
} elseif ($status == 'completed') {
    $status_badge_class = 'bg-success';
    $status_text = 'Selesai';
} elseif ($status == 'cancelled') {
    $status_badge_class = 'bg-danger';
    $status_text = 'Dibatalkan';
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success/Status Message -->
            <div class="text-center mb-4">
                <div class="mb-4">
                    <?php if ($status == 'paid' || $status == 'completed' || $status == 'shipped'): ?>
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    <?php elseif ($status == 'cancelled'): ?>
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                    <?php else: ?>
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 5rem;"></i>
                    <?php endif; ?>
                </div>
                <h2 class="fw-bold mb-2">
                    <?php 
                    if ($status == 'paid') {
                        echo 'Pesanan Berhasil Dibuat!';
                    } elseif ($status == 'cancelled') {
                        echo 'Pesanan Dibatalkan';
                    } else {
                        echo 'Pesanan Menunggu Pembayaran';
                    }
                    ?>
                </h2>
                <p class="text-muted mb-0">Terima kasih telah berbelanja di TechShop</p>
                <p class="text-muted">Nomor Pesanan: <strong>#<?php echo $order['id']; ?></strong></p>
            </div>

            <!-- Payment Status Alert -->
            <div class="alert alert-<?php echo $status == 'paid' ? 'success' : ($status == 'cancelled' ? 'danger' : 'warning'); ?> mb-4" role="alert">
                <i class="bi bi-info-circle"></i>
                <strong>Status Pesanan:</strong>
                <?php 
                if ($status == 'paid') {
                    echo 'Pesanan Anda telah dikonfirmasi dan sedang diproses.';
                } elseif ($status == 'cancelled') {
                    echo 'Pesanan Anda telah dibatalkan.';
                } else {
                    echo 'Pesanan Anda sedang menunggu konfirmasi pembayaran.';
                }
                ?>
            </div>
            
            <?php if ($status == 'pending' && !empty($order['snap_token'])): ?>
            <!-- Pay Now Button -->
            <div class="d-grid gap-2 mb-4">
                <button id="pay-now-btn" class="btn btn-primary btn-lg">
                    <i class="bi bi-credit-card"></i> Bayar Sekarang
                </button>
            </div>
            <?php endif; ?>

            <!-- Order Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Detail Pesanan</h5>
                        <span class="badge <?php echo $status_badge_class; ?>">
                            <?php echo $status_text; ?>
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Order Timeline -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="fw-bold mb-2"><i class="bi bi-calendar-event"></i> Tanggal Pesanan</h6>
                                <p class="mb-0"><?php echo date('d F Y, H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-2"><i class="bi bi-credit-card"></i> Metode Pembayaran</h6>
                                <p class="mb-0">
                                    <?php 
                                    if (!empty($order['payment_method'])) {
                                        $method = strtoupper($order['payment_method']);
                                        if (strpos($method, 'CREDIT') !== false) echo '💳 Kartu Kredit';
                                        elseif (strpos($method, 'DEBIT') !== false) echo '🏦 Kartu Debit';
                                        elseif (strpos($method, 'BANK') !== false) echo '🏧 Transfer Bank';
                                        else echo $method;
                                    } else {
                                        echo 'Belum ditentukan';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt"></i> Alamat Pengiriman</h6>
                            <p class="mb-0">
                                <?php echo nl2br(clean($order['shipping_address'])); ?><br>
                                <strong><?php echo clean($order['shipping_city']); ?> <?php echo clean($order['shipping_postal_code']); ?></strong><br>
                                <i class="bi bi-telephone"></i> <?php echo clean($order['shipping_phone']); ?>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Order Items Table -->
                    <h6 class="fw-bold mb-3">Daftar Produk</h6>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $items_result = mysqli_query($conn, $items_query);
                                while ($item = mysqli_fetch_assoc($items_result)): 
                                ?>
                                    <tr>
                                        <td><?php echo clean($item['product_name']); ?></td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end"><?php echo format_rupiah($item['price']); ?></td>
                                        <td class="text-end fw-bold"><?php echo format_rupiah($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="border-top">
                                    <td colspan="3" class="text-end fw-bold">Subtotal</td>
                                    <td class="text-end fw-bold"><?php echo format_rupiah($order['total']); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Ongkos Kirim</td>
                                    <td class="text-end fw-bold text-success">GRATIS</td>
                                </tr>
                                <tr class="bg-light border-top border-bottom">
                                    <td colspan="3" class="text-end fw-bold">Total Pembayaran</td>
                                    <td class="text-end">
                                        <h5 class="text-primary mb-0 fw-bold"><?php echo format_rupiah($order['total']); ?></h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?php if (!empty($order['notes'])): ?>
                        <div class="alert alert-info mt-3 mb-0">
                            <strong><i class="bi bi-sticky"></i> Catatan:</strong> <?php echo clean($order['notes']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-checklist"></i> Langkah Selanjutnya</h5>
                </div>
                <div class="card-body p-4">
                    <ol class="mb-0">
                        <?php if ($status == 'paid' || $status == 'completed'): ?>
                            <li class="mb-2"><strong>✓ Pesanan Dikonfirmasi</strong> - Pesanan Anda sudah dikonfirmasi</li>
                            <li class="mb-2">Kami akan menyiapkan pesanan Anda dalam 1-2 hari kerja</li>
                            <li class="mb-2">Anda akan menerima notifikasi pengiriman via email dan SMS</li>
                            <li class="mb-0">Cek status pesanan di halaman <strong><a href="<?php echo BASE_URL; ?>/user/orders.php">Pesanan Saya</a></strong></li>
                        <?php else: ?>
                            <li class="mb-2">Pesanan Anda sedang menunggu pembayaran</li>
                            <li class="mb-2">Silakan melakukan pembayaran melalui dashboard akun Anda</li>
                            <li class="mb-2">Setelah pembayaran dikonfirmasi, pesanan akan segera diproses</li>
                            <li class="mb-0">Pertanyaan? <a href="<?php echo BASE_URL; ?>/contact.php">Hubungi kami</a></li>
                        <?php endif; ?>
                    </ol>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-center mb-4">
                <a href="<?php echo BASE_URL; ?>/user/orders.php" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-bag-check"></i> Lihat Pesanan Saya
                </a>
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-outline-secondary btn-lg px-5">
                    <i class="bi bi-shop"></i> Lanjut Belanja
                </a>
            </div>

            <!-- Support Info -->
            <div class="alert alert-light border text-center">
                <p class="mb-1"><small>Butuh bantuan? Hubungi customer service kami</small></p>
                <p class="mb-0">
                    <i class="bi bi-telephone"></i> 
                    <strong>(+62) 123-4567-890</strong> 
                    | 
                    <i class="bi bi-envelope"></i> 
                    <strong>support@techshop.com</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<?php if ($status == 'pending' && !empty($order['snap_token'])): ?>
<!-- Load Midtrans Snap JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>
<script>
window.addEventListener('load', function() {
    const payNowBtn = document.getElementById('pay-now-btn');
    if (payNowBtn) {
        payNowBtn.addEventListener('click', function() {
            if (typeof snap === 'undefined') {
                alert('Payment gateway sedang dimuat. Silakan coba lagi.');
                return;
            }
            
            snap.pay('<?php echo $order['snap_token']; ?>', {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    window.location.href = '<?php echo BASE_URL; ?>/user/orders.php';
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    window.location.href = '<?php echo BASE_URL; ?>/user/orders.php';
                },
                onError: function(result) {
                    console.error('Payment error:', result);
                    alert('Pembayaran gagal! Silakan coba lagi.');
                },
                onClose: function() {
                    console.log('Payment modal closed');
                }
            });
        });
    }
});
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
