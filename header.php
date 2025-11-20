<?php
require_once 'db.php';
require_once 'config.php';

// Get categories for menu
$categories_query = "SELECT * FROM categories ORDER BY cat_title";
$categories_result = mysqli_query($conn, $categories_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>TechShop - E-Commerce</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>

<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/index.php">
            <?php if (file_exists('img/logo.png')): ?>
                <img src="<?php echo BASE_URL; ?>/img/logo.png?v=<?php echo filemtime('img/logo.png'); ?>" 
                     alt="TechShop Logo" 
                     class="navbar-logo">
            <?php else: ?>
                <i class="bi bi-shop"></i>
            <?php endif; ?>
            TechShop
        </a>

        <!-- Cart Icon (Mobile - Always Visible) -->
        <?php if (is_logged_in()): ?>
            <a class="nav-link position-relative d-lg-none text-white p-0 ms-auto" href="<?php echo BASE_URL; ?>/cart.php">
                <i class="bi bi-cart3 fs-5"></i>
                <?php $cart_count = get_cart_count(); ?>
                <?php if ($cart_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge-mobile">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <button class="navbar-toggler navbar-toggler-sm ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Categories Dropdown -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-grid"></i> Kategori
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/products.php">Semua Produk</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>/products.php?cat=<?php echo $cat['cat_id']; ?>">
                                    <?php echo clean($cat['cat_title']); ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/products.php">Produk</a>
                </li>
            </ul>

            <!-- Search Form -->
            <form class="d-flex me-3" action="<?php echo BASE_URL; ?>/products.php" method="GET">
                <div class="input-group">
                    <input class="form-control form-control-sm" type="search" placeholder="Cari produk..." name="search" value="<?php echo isset($_GET['search']) ? clean($_GET['search']) : ''; ?>">
                    <button class="btn btn-outline-light btn-sm" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- User Menu -->
            <ul class="navbar-nav">
                <?php if (is_logged_in()): ?>
                    <!-- Cart (Desktop Only) -->
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link position-relative" href="<?php echo BASE_URL; ?>/cart.php">
                            <i class="bi bi-cart3 fs-5"></i>
                            <?php if ($cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo clean($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (is_admin()): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php"><i class="bi bi-speedometer2"></i> Dashboard Admin</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/user/index.php"><i class="bi bi-person"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/user/orders.php"><i class="bi bi-bag"></i> Pesanan Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/register.php">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php
$flash = get_flash();
if ($flash):
?>
<div class="container mt-3">
    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo clean($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>
