<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin Panel - TechShop</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">

    <!-- Custom Admin CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e293b 0%, #334155 100%);
            padding: 0;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #3b82f6;
        }

        .sidebar-nav .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: inline-block;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="adminSidebar">
    <div class="sidebar-header text-white">
        <h4 class="mb-0 fw-bold">
            <?php if (file_exists('../img/logo.png')): ?>
                <img src="<?php echo BASE_URL; ?>/img/logo.png?v=<?php echo filemtime('../img/logo.png'); ?>" 
                     alt="TechShop Logo" 
                     class="admin-logo">
            <?php else: ?>
                <i class="bi bi-shop"></i>
            <?php endif; ?>
            TechShop
        </h4>
        <small class="text-white-50">Admin Panel</small>
    </div>

    <nav class="sidebar-nav">
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/products.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php' || basename($_SERVER['PHP_SELF']) == 'add_product.php' || basename($_SERVER['PHP_SELF']) == 'edit_product.php') ? 'active' : ''; ?>">
            <i class="bi bi-box-seam"></i> Produk
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : ''; ?>">
            <i class="bi bi-tags"></i> Kategori
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>">
            <i class="bi bi-receipt"></i> Pesanan
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/users.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">
            <i class="bi bi-people"></i> Users
        </a>
        <hr class="my-3 border-secondary">
        <a href="<?php echo BASE_URL; ?>/index.php" class="nav-link">
            <i class="bi bi-globe"></i> Lihat Website
        </a>
        <a href="<?php echo BASE_URL; ?>/logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Bar -->
    <div class="topbar">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold"><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></h5>
            </div>
            <div>
                <span class="text-muted me-3">
                    <i class="bi bi-person-circle"></i> <?php echo clean($_SESSION['user_name']); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php
    $flash = get_flash();
    if ($flash):
    ?>
    <div class="container-fluid mt-3">
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo clean($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Page Content -->
