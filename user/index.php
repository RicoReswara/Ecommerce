<?php
$page_title = "Profil Saya";
require_once '../db.php';
require_once '../config.php';

// Require login
require_login();

$user_id = $_SESSION['user_id'];

// Get user data
$user_query = "SELECT u.*, ui.*
               FROM users u
               LEFT JOIN user_info ui ON u.id = ui.user_id
               WHERE u.id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get statistics
$stats = [];

// Total orders
$orders_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = $user_id";
$result = mysqli_query($conn, $orders_query);
$stats['orders'] = mysqli_fetch_assoc($result)['total'];

// Total spent
$spent_query = "SELECT SUM(total) as total FROM orders WHERE user_id = $user_id AND status != 'cancelled'";
$result = mysqli_query($conn, $spent_query);
$stats['spent'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Pending orders
$pending_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = $user_id AND status = 'pending'";
$result = mysqli_query($conn, $pending_query);
$stats['pending'] = mysqli_fetch_assoc($result)['total'];

include '../header.php';
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-person-circle"></i> Profil Saya</h2>

    <div class="row g-4">
        <!-- User Info Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1"><?php echo clean($user['name']); ?></h4>
                    <p class="text-muted mb-3"><?php echo clean($user['email']); ?></p>

                    <?php if ($user['is_admin'] == 1): ?>
                        <span class="badge bg-danger mb-3">Administrator</span>
                    <?php else: ?>
                        <span class="badge bg-primary mb-3">Customer</span>
                    <?php endif; ?>

                    <p class="small text-muted mb-0">
                        <i class="bi bi-calendar"></i>
                        Bergabung sejak <?php echo date('d F Y', strtotime($user['created_at'])); ?>
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Menu</h6>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo BASE_URL; ?>/user/index.php" class="list-group-item list-group-item-action active">
                            <i class="bi bi-person"></i> Profil Saya
                        </a>
                        <a href="<?php echo BASE_URL; ?>/user/orders.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-bag"></i> Pesanan Saya
                        </a>
                        <a href="<?php echo BASE_URL; ?>/cart.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-cart"></i> Keranjang
                        </a>
                        <a href="<?php echo BASE_URL; ?>/logout.php" class="list-group-item list-group-item-action text-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics & Info -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body text-center">
                            <h2 class="fw-bold mb-0"><?php echo $stats['orders']; ?></h2>
                            <p class="mb-0">Total Pesanan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-success text-white">
                        <div class="card-body text-center">
                            <h5 class="fw-bold mb-0"><?php echo format_rupiah($stats['spent']); ?></h5>
                            <p class="mb-0">Total Belanja</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-warning text-white">
                        <div class="card-body text-center">
                            <h2 class="fw-bold mb-0"><?php echo $stats['pending']; ?></h2>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi Profil</h5>
                        <a href="<?php echo BASE_URL; ?>/user/profile.php" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit Profil
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nama Lengkap</label>
                            <p class="fw-bold mb-0"><?php echo clean($user['name']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Email</label>
                            <p class="fw-bold mb-0"><?php echo clean($user['email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nomor Telepon</label>
                            <p class="fw-bold mb-0"><?php echo !empty($user['phone']) ? clean($user['phone']) : '-'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Kota</label>
                            <p class="fw-bold mb-0"><?php echo !empty($user['city']) ? clean($user['city']) : '-'; ?></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Alamat</label>
                            <p class="fw-bold mb-0"><?php echo !empty($user['address']) ? nl2br(clean($user['address'])) : '-'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
