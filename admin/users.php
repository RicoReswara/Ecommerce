<?php
$page_title = "Kelola Users";
require_once '../db.php';
require_once '../config.php';

// Require admin
require_admin();

// Handle toggle admin status
if (isset($_GET['toggle_admin'])) {
    $user_id = intval($_GET['toggle_admin']);

    // Don't allow changing own admin status
    if ($user_id != $_SESSION['user_id']) {
        $query = "UPDATE users SET is_admin = IF(is_admin = 1, 0, 1) WHERE id = $user_id";
        if (mysqli_query($conn, $query)) {
            set_flash('success', 'Status admin berhasil diubah.');
        } else {
            set_flash('danger', 'Gagal mengubah status admin.');
        }
    } else {
        set_flash('danger', 'Anda tidak bisa mengubah status admin sendiri.');
    }
    redirect('admin/users.php');
}

// Handle delete user
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);

    // Don't allow deleting own account
    if ($user_id != $_SESSION['user_id']) {
        $delete = "DELETE FROM users WHERE id = $user_id AND is_admin = 0";
        if (mysqli_query($conn, $delete)) {
            set_flash('success', 'User berhasil dihapus.');
        } else {
            set_flash('danger', 'Gagal menghapus user.');
        }
    } else {
        set_flash('danger', 'Anda tidak bisa menghapus akun sendiri.');
    }
    redirect('admin/users.php');
}

// Get users with statistics
$query = "SELECT u.*,
          (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
          (SELECT SUM(total) FROM orders WHERE user_id = u.id AND status != 'cancelled') as total_spent
          FROM users u
          ORDER BY u.id DESC";
$users = mysqli_query($conn, $query);

include 'header_admin.php';
?>

<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-people"></i> Kelola Users</h2>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Pesanan</th>
                            <th>Total Belanja</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($users) > 0): ?>
                            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                                <tr>
                                    <td><strong>#<?php echo $user['id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo clean($user['name']); ?></strong>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge bg-info">You</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo clean($user['email']); ?></td>
                                    <td>
                                        <?php if ($user['is_admin'] == 1): ?>
                                            <span class="badge bg-danger">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Customer</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $user['order_count']; ?> pesanan</td>
                                    <td><?php echo format_rupiah($user['total_spent'] ?? 0); ?></td>
                                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="?toggle_admin=<?php echo $user['id']; ?>"
                                                   class="btn btn-outline-warning"
                                                   title="Toggle Admin">
                                                    <i class="bi bi-shield-check"></i>
                                                </a>
                                                <?php if ($user['is_admin'] == 0): ?>
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-delete-user"
                                                            data-id="<?php echo $user['id']; ?>"
                                                            data-name="<?php echo clean($user['name']); ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    Current User
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Tidak ada user ditemukan.
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
