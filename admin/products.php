<?php
$page_title = "Kelola Produk";
require_once '../db.php';
require_once '../config.php';

// Require admin
require_admin();

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Get product image to delete
    $query = "SELECT image FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        delete_product_image($row['image']);
    }

    // Delete product
    $delete_query = "DELETE FROM products WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        set_flash('success', 'Produk berhasil dihapus.');
    } else {
        set_flash('danger', 'Gagal menghapus produk.');
    }
    redirect('admin/products.php');
}

// Get products
$search = isset($_GET['search']) ? escape($_GET['search']) : '';
$where = !empty($search) ? "WHERE p.name LIKE '%$search%' OR p.description LIKE '%$search%'" : "";

$query = "SELECT p.*, c.cat_title
          FROM products p
          LEFT JOIN categories c ON p.product_cat = c.cat_id
          $where
          ORDER BY p.id DESC";
$products = mysqli_query($conn, $query);

include 'header_admin.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-box-seam"></i> Kelola Produk</h2>
        <a href="<?php echo BASE_URL; ?>/admin/add_product.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Produk
        </a>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="search"
                           placeholder="Cari produk..."
                           value="<?php echo clean($search); ?>">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Brand</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Featured</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($products) > 0): ?>
                            <?php while ($product = mysqli_fetch_assoc($products)): ?>
                                <tr>
                                    <td><strong>#<?php echo $product['id']; ?></strong></td>
                                    <td>
                                        <img src="<?php echo get_product_image($product['image']); ?>"
                                             alt="<?php echo clean($product['name']); ?>"
                                             style="width: 50px; height: 50px; object-fit: cover;"
                                             class="rounded">
                                    </td>
                                    <td>
                                        <strong><?php echo clean($product['name']); ?></strong>
                                    </td>
                                    <td><?php echo clean($product['cat_title'] ?? '-'); ?></td>
                                    <td><?php echo clean($product['product_brand'] ?? '-'); ?></td>
                                    <td><?php echo format_rupiah($product['price']); ?></td>
                                    <td>
                                        <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                                            <span class="badge bg-warning"><?php echo $product['stock']; ?></span>
                                        <?php elseif ($product['stock'] == 0): ?>
                                            <span class="badge bg-danger">Habis</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $product['stock']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['featured']): ?>
                                            <span class="badge bg-danger">Ya</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo BASE_URL; ?>/admin/edit_product.php?id=<?php echo $product['id']; ?>"
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-delete-product"
                                                    data-id="<?php echo $product['id']; ?>"
                                                    data-name="<?php echo clean($product['name']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Tidak ada produk ditemukan.
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
