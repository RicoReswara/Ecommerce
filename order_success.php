<?php
$page_title = "Pesanan Berhasil";
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
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="text-center mb-4">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h2 class="fw-bold mb-2">Pesanan Berhasil Dibuat!</h2>
                <p class="text-muted">Terima kasih telah berbelanja di TechShop</p>
            </div>

            <!-- Order Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Order #<?php echo $order['id']; ?></h5>
                        <span class="badge bg-light text-dark">
                            <?php echo strtoupper($order['status']); ?>
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3"><i class="bi bi-calendar"></i> Tanggal Pesanan</h6>
                            <p class="mb-0"><?php echo date('d F Y, H:i', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt"></i> Alamat Pengiriman</h6>
                            <p class="mb-1"><?php echo nl2br(clean($order['shipping_address'])); ?></p>
                            <p class="mb-1"><?php echo clean($order['shipping_city']); ?> <?php echo clean($order['shipping_postal_code']); ?></p>
                            <p class="mb-0"><i class="bi bi-telephone"></i> <?php echo clean($order['shipping_phone']); ?></p>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">Detail Pesanan</h6>
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
                                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                    <tr>
                                        <td><?php echo clean($item['product_name']); ?></td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end"><?php echo format_rupiah($item['price']); ?></td>
                                        <td class="text-end fw-bold"><?php echo format_rupiah($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                    <td class="text-end">
                                        <h5 class="text-success mb-0"><?php echo format_rupiah($order['total']); ?></h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?php if (!empty($order['notes'])): ?>
                        <div class="alert alert-info mt-3 mb-0">
                            <strong>Catatan:</strong> <?php echo clean($order['notes']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Langkah Selanjutnya</h5>
                    <ol class="mb-0">
                        <li class="mb-2">Kami akan mengirimkan konfirmasi pesanan ke email Anda</li>
                        <li class="mb-2">Pesanan Anda akan diproses dalam 1-2 hari kerja</li>
                        <li class="mb-2">Anda akan menerima nomor resi pengiriman setelah pesanan dikirim</li>
                        <li class="mb-0">Cek status pesanan Anda di halaman <strong>Pesanan Saya</strong></li>
                    </ol>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="<?php echo BASE_URL; ?>/user/orders.php" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-bag"></i> Lihat Pesanan Saya
                </a>
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-outline-secondary btn-lg px-5">
                    <i class="bi bi-shop"></i> Lanjut Belanja
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
