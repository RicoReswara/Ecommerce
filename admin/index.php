<?php
require_once '../db.php';
require_once '../config.php';

// Require admin
require_admin();

// Get statistics
$stats = [];

// Total products
$products_query = "SELECT COUNT(*) as total FROM products";
$result = mysqli_query($conn, $products_query);
$stats['products'] = mysqli_fetch_assoc($result)['total'];

// Total orders
$orders_query = "SELECT COUNT(*) as total FROM orders";
$result = mysqli_query($conn, $orders_query);
$stats['orders'] = mysqli_fetch_assoc($result)['total'];

// Total users
$users_query = "SELECT COUNT(*) as total FROM users WHERE is_admin = 0";
$result = mysqli_query($conn, $users_query);
$stats['users'] = mysqli_fetch_assoc($result)['total'];

// Total revenue
$revenue_query = "SELECT SUM(total) as total FROM orders WHERE status != 'cancelled'";
$result = mysqli_query($conn, $revenue_query);
$stats['revenue'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Recent orders
$recent_orders_query = "SELECT o.*, u.name as user_name
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        ORDER BY o.created_at DESC
                        LIMIT 10";
$recent_orders = mysqli_query($conn, $recent_orders_query);

// Low stock products
$low_stock_query = "SELECT * FROM products WHERE stock <= 5 AND stock > 0 ORDER BY stock ASC LIMIT 5";
$low_stock = mysqli_query($conn, $low_stock_query);

$page_title = "Admin Dashboard";
include 'header_admin.php';
?>

<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Produk</h6>
                            <h2 class="mb-0 fw-bold"><?php echo $stats['products']; ?></h2>
                        </div>
                        <i class="bi bi-box-seam fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Pesanan</h6>
                            <h2 class="mb-0 fw-bold"><?php echo $stats['orders']; ?></h2>
                        </div>
                        <i class="bi bi-receipt fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Customer</h6>
                            <h2 class="mb-0 fw-bold"><?php echo $stats['users']; ?></h2>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Pendapatan</h6>
                            <h4 class="mb-0 fw-bold"><?php echo format_rupiah($stats['revenue']); ?></h4>
                        </div>
                        <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Pesanan Terbaru</h5>
                        <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                    <tr>
                                        <td><strong>#<?php echo $order['id']; ?></strong></td>
                                        <td><?php echo clean($order['user_name']); ?></td>
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
                                        <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning"></i> Stok Menipis</h5>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($low_stock) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while ($product = mysqli_fetch_assoc($low_stock)): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo clean(truncate($product['name'], 40)); ?></h6>
                                            <small class="text-muted">Stok:
                                                <span class="badge bg-danger"><?php echo $product['stock']; ?></span>
                                            </small>
                                        </div>
                                        <a href="<?php echo BASE_URL; ?>/admin/edit_product.php?id=<?php echo $product['id']; ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Semua produk memiliki stok yang cukup.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>
