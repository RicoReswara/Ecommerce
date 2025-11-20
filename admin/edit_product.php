<?php
$page_title = "Edit Produk";
require_once '../db.php';
require_once '../config.php';

// Require admin
require_admin();

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id == 0) {
    set_flash('danger', 'Produk tidak ditemukan.');
    redirect('admin/products.php');
}

// Get product
$query = "SELECT * FROM products WHERE id = $product_id LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    set_flash('danger', 'Produk tidak ditemukan.');
    redirect('admin/products.php');
}

$product = mysqli_fetch_assoc($result);

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY cat_title";
$categories = mysqli_query($conn, $categories_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_cat = intval($_POST['product_cat']);
    $product_brand = escape($_POST['product_brand']);
    $name = escape($_POST['name']);
    $description = escape($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Handle image upload
    $image_filename = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Delete old image
        if (!empty($product['image'])) {
            delete_product_image($product['image']);
        }

        $upload_result = upload_product_image($_FILES['image']);
        if ($upload_result['success']) {
            $image_filename = $upload_result['filename'];
        } else {
            set_flash('danger', $upload_result['message']);
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $product_id);
            exit;
        }
    }

    // Update product
    $update_query = "UPDATE products SET
                     product_cat = $product_cat,
                     product_brand = '$product_brand',
                     name = '$name',
                     description = '$description',
                     price = $price,
                     stock = $stock,
                     image = '$image_filename',
                     featured = $featured
                     WHERE id = $product_id";

    if (mysqli_query($conn, $update_query)) {
        set_flash('success', 'Produk berhasil diupdate.');
        redirect('admin/products.php');
    } else {
        set_flash('danger', 'Gagal mengupdate produk: ' . mysqli_error($conn));
    }
}

include 'header_admin.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Produk: <?php echo clean($product['name']); ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Produk *</label>
                            <input type="text" class="form-control" name="name"
                                   value="<?php echo clean($product['name']); ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori *</label>
                                <select class="form-select" name="product_cat" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?php echo $cat['cat_id']; ?>"
                                                <?php echo ($cat['cat_id'] == $product['product_cat']) ? 'selected' : ''; ?>>
                                            <?php echo clean($cat['cat_title']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Brand</label>
                                <input type="text" class="form-control" name="product_brand"
                                       value="<?php echo clean($product['product_brand']); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="4"><?php echo clean($product['description']); ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Harga (Rp) *</label>
                                <input type="number" class="form-control" name="price"
                                       value="<?php echo $product['price']; ?>" min="0" step="0.01" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Stok *</label>
                                <input type="number" class="form-control" name="stock"
                                       value="<?php echo $product['stock']; ?>" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Gambar Produk</label>
                            <?php if (!empty($product['image'])): ?>
                                <div class="mb-2">
                                    <img src="<?php echo get_product_image($product['image']); ?>"
                                         alt="Current Image" class="img-thumbnail" style="max-width: 200px;">
                                    <p class="small text-muted mb-0">Gambar saat ini</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="featured" id="featured"
                                       <?php echo ($product['featured'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="featured">
                                    Tandai sebagai <strong>Produk Unggulan</strong>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Produk
                            </button>
                            <a href="<?php echo BASE_URL; ?>/admin/products.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Info Produk</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>ID:</strong> #<?php echo $product['id']; ?></p>
                    <p class="mb-2"><strong>Dibuat:</strong> <?php echo date('d M Y', strtotime($product['created_at'])); ?></p>
                    <p class="mb-0"><strong>Status:</strong>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="badge bg-success">Tersedia</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Stok Habis</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>
