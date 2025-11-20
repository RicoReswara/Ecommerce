<?php
$page_title = "Kelola Pesanan";
require_once '../db.php';
require_once '../config.php';

// Require admin
require_admin();

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = escape($_POST['status']);

    $update = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    if (mysqli_query($conn, $update)) {
        set_flash('success', 'Status pesanan berhasil diupdate.');
    } else {
        set_flash('danger', 'Gagal mengupdate status pesanan.');
    }
    redirect('admin/orders.php');
}

// Get filter
$filter_status = isset($_GET['status']) ? escape($_GET['status']) : '';
$where = !empty($filter_status) ? "WHERE o.status = '$filter_status'" : "";

// Get orders
$query = "SELECT o.*, u.name as customer_name, u.email as customer_email,
          (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
          FROM orders o
          JOIN users u ON o.user_id = u.id
          $where
          ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $query);

include 'header_admin.php';
?>

<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-receipt"></i> Kelola Pesanan</h2>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <strong>Filter Status:</strong>
                </div>
                <div class="col-auto">
                    <a href="<?php echo BASE_URL; ?>/admin/orders.php"
                       class="btn btn-sm <?php echo empty($filter_status) ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                        Semua
                    </a>
                </div>
                <div class="col-auto">
                    <a href="?status=pending"
                       class="btn btn-sm <?php echo ($filter_status == 'pending') ? 'btn-warning' : 'btn-outline-warning'; ?>">
                        Pending
                    </a>
                </div>
                <div class="col-auto">
                    <a href="?status=paid"
                       class="btn btn-sm <?php echo ($filter_status == 'paid') ? 'btn-info' : 'btn-outline-info'; ?>">
                        Paid
                    </a>
                </div>
                <div class="col-auto">
                    <a href="?status=shipped"
                       class="btn btn-sm <?php echo ($filter_status == 'shipped') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        Shipped
                    </a>
                </div>
                <div class="col-auto">
                    <a href="?status=completed"
                       class="btn btn-sm <?php echo ($filter_status == 'completed') ? 'btn-success' : 'btn-outline-success'; ?>">
                        Completed
                    </a>
                </div>
                <div class="col-auto">
                    <a href="?status=cancelled"
                       class="btn btn-sm <?php echo ($filter_status == 'cancelled') ? 'btn-danger' : 'btn-outline-danger'; ?>">
                        Cancelled
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($orders) > 0): ?>
                            <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                                <tr>
                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo clean($order['customer_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo clean($order['customer_email']); ?></small>
                                    </td>
                                    <td><?php echo $order['item_count']; ?> item</td>
                                    <td><?php echo format_rupiah($order['total']); ?></td>
                                    <td>
                                        <?php
                                        $badge_class = '';
                                        switch ($order['status']) {
                                            case 'pending': $badge_class = 'bg-warning'; break;
                                            case 'paid': $badge_class = 'bg-info'; break;
                                            case 'shipped': $badge_class = 'bg-primary'; break;
                                            case 'completed': $badge_class = 'bg-success'; break;
                                            case 'cancelled': $badge_class = 'bg-danger'; break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Order Detail Modal -->
                                <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Pesanan #<?php echo $order['id']; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold">Informasi Customer</h6>
                                                        <p class="mb-1"><strong>Nama:</strong> <?php echo clean($order['customer_name']); ?></p>
                                                        <p class="mb-1"><strong>Email:</strong> <?php echo clean($order['customer_email']); ?></p>
                                                        <p class="mb-1"><strong>Telepon:</strong> <?php echo clean($order['shipping_phone']); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold">Alamat Pengiriman</h6>
                                                        <p class="mb-1"><?php echo nl2br(clean($order['shipping_address'])); ?></p>
                                                        <p class="mb-0"><?php echo clean($order['shipping_city']); ?> <?php echo clean($order['shipping_postal_code']); ?></p>
                                                    </div>
                                                </div>

                                                <?php
                                                // Get order items
                                                $items_query = "SELECT * FROM order_items WHERE order_id = {$order['id']}";
                                                $items = mysqli_query($conn, $items_query);
                                                ?>

                                                <h6 class="fw-bold mb-3">Item Pesanan</h6>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Produk</th>
                                                            <th>Qty</th>
                                                            <th>Harga</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while ($item = mysqli_fetch_assoc($items)): ?>
                                                            <tr>
                                                                <td><?php echo clean($item['product_name']); ?></td>
                                                                <td><?php echo $item['quantity']; ?></td>
                                                                <td><?php echo format_rupiah($item['price']); ?></td>
                                                                <td><?php echo format_rupiah($item['price'] * $item['quantity']); ?></td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                            <td><strong><?php echo format_rupiah($order['total']); ?></strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>

                                                <hr>

                                                <h6 class="fw-bold mb-3">Update Status</h6>
                                                <form method="POST" action="">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <select class="form-select" name="status" required>
                                                                <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="paid" <?php echo ($order['status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                                                                <option value="shipped" <?php echo ($order['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                                                <option value="completed" <?php echo ($order['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                                                <option value="cancelled" <?php echo ($order['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button type="submit" name="update_status" class="btn btn-primary w-100">
                                                                Update Status
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Tidak ada pesanan ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>
