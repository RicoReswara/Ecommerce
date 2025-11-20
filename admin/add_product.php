<?php
$page_title = "Tambah Produk";
require_once '../db.php';
require_once '../config.php';

// Require admin
require_admin();

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
    $image_filename = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_result = upload_product_image($_FILES['image']);
        if ($upload_result['success']) {
            $image_filename = $upload_result['filename'];
        } else {
            set_flash('danger', $upload_result['message']);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    // Insert product
    $insert_query = "INSERT INTO products (product_cat, product_brand, name, description, price, stock, image, featured)
                    VALUES ($product_cat, '$product_brand', '$name', '$description', $price, $stock, '$image_filename', $featured)";

    if (mysqli_query($conn, $insert_query)) {
        set_flash('success', 'Produk berhasil ditambahkan.');
        redirect('admin/products.php');
    } else {
        set_flash('danger', 'Gagal menambahkan produk: ' . mysqli_error($conn));
    }
}

include 'header_admin.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Produk Baru</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Produk *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori *</label>
                                <select class="form-select" name="product_cat" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?php echo $cat['cat_id']; ?>">
                                            <?php echo clean($cat['cat_title']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Brand</label>
                                <input type="text" class="form-control" name="product_brand"
                                       placeholder="Contoh: Asus, Samsung, Canon">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="4"
                                      placeholder="Deskripsi lengkap produk..."></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Harga (Rp) *</label>
                                <input type="number" class="form-control" name="price" min="0" step="0.01" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Stok *</label>
                                <input type="number" class="form-control" name="stock" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Gambar Produk</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="featured" id="featured">
                                <label class="form-check-label" for="featured">
                                    Tandai sebagai <strong>Produk Unggulan</strong>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Produk
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Panduan</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li class="mb-2">Pastikan nama produk jelas dan deskriptif</li>
                        <li class="mb-2">Upload gambar dengan kualitas baik</li>
                        <li class="mb-2">Isi stok dengan jumlah yang akurat</li>
                        <li class="mb-2">Produk unggulan akan ditampilkan di homepage</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_admin.php'; ?>
