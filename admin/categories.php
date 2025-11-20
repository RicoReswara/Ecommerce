<?php
$page_title = "Kelola Kategori";
require_once '../db.php';
require_once '../config.php';

// Require admin
require_admin();

// Handle add category
if (isset($_POST['add_category'])) {
    $cat_title = escape($_POST['cat_title']);
    $cat_description = escape($_POST['cat_description']);

    if (!empty($cat_title)) {
        $insert = "INSERT INTO categories (cat_title, cat_description) VALUES ('$cat_title', '$cat_description')";
        if (mysqli_query($conn, $insert)) {
            set_flash('success', 'Kategori berhasil ditambahkan.');
        } else {
            set_flash('danger', 'Gagal menambahkan kategori.');
        }
        redirect('admin/categories.php');
    }
}

// Handle edit category
if (isset($_POST['edit_category'])) {
    $cat_id = intval($_POST['cat_id']);
    $cat_title = escape($_POST['cat_title']);
    $cat_description = escape($_POST['cat_description']);

    $update = "UPDATE categories SET cat_title = '$cat_title', cat_description = '$cat_description' WHERE cat_id = $cat_id";
    if (mysqli_query($conn, $update)) {
        set_flash('success', 'Kategori berhasil diupdate.');
    } else {
        set_flash('danger', 'Gagal mengupdate kategori.');
    }
    redirect('admin/categories.php');
}

// Handle delete category
if (isset($_GET['delete'])) {
    $cat_id = intval($_GET['delete']);

    // Check if category has products
    $check = "SELECT COUNT(*) as count FROM products WHERE product_cat = $cat_id";
    $result = mysqli_query($conn, $check);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        set_flash('danger', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
    } else {
        $delete = "DELETE FROM categories WHERE cat_id = $cat_id";
        if (mysqli_query($conn, $delete)) {
            set_flash('success', 'Kategori berhasil dihapus.');
        } else {
            set_flash('danger', 'Gagal menghapus kategori.');
        }
    }
    redirect('admin/categories.php');
}

// Get categories with product count
$query = "SELECT c.*, COUNT(p.id) as product_count
          FROM categories c
          LEFT JOIN products p ON c.cat_id = p.product_cat
          GROUP BY c.cat_id
          ORDER BY c.cat_title";
$categories = mysqli_query($conn, $query);

// Get category for edit
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_query = "SELECT * FROM categories WHERE cat_id = $edit_id";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_category = mysqli_fetch_assoc($edit_result);
}

include 'header_admin.php';
?>

<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-tags"></i> Kelola Kategori</h2>

    <div class="row g-4">
        <!-- Categories List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Daftar Kategori</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah Produk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                    <tr>
                                        <td><strong>#<?php echo $cat['cat_id']; ?></strong></td>
                                        <td><strong><?php echo clean($cat['cat_title']); ?></strong></td>
                                        <td><?php echo clean(truncate($cat['cat_description'] ?? '', 50)); ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo $cat['product_count']; ?> produk</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="?edit=<?php echo $cat['cat_id']; ?>" class="btn btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-outline-danger btn-delete-category"
                                                        data-id="<?php echo $cat['cat_id']; ?>"
                                                        data-name="<?php echo clean($cat['cat_title']); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <?php echo $edit_category ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <?php if ($edit_category): ?>
                            <input type="hidden" name="cat_id" value="<?php echo $edit_category['cat_id']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kategori *</label>
                            <input type="text" class="form-control" name="cat_title" required
                                   value="<?php echo $edit_category ? clean($edit_category['cat_title']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control" name="cat_description" rows="3"><?php echo $edit_category ? clean($edit_category['cat_description']) : ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <?php if ($edit_category): ?>
                                <button type="submit" name="edit_category" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Kategori
                                </button>
                                <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_category" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Tambah Kategori
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>
