<?php
/**
 * MAISON ÉLAN - Admin Header & Layout
 */

require_once __DIR__ . '/admin-auth.php';
require_admin();

$adminUser = get_admin_user();
$currentAdminPage = basename($_SERVER['PHP_SELF']);
$adminTitle = isset($adminTitle) ? $adminTitle . " — Atelier Admin" : "Atelier Administration — MAISON ÉLAN";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminTitle) ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/admin.css?v=1.0">
</head>
<body class="admin-body">

    <div class="admin-wrapper">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <a href="<?= BASE_URL ?>/admin/index.php" class="sidebar-logo">
                    <span class="logo-text">MAISON ÉLAN</span>
                    <span class="logo-sub">ATELIER CONSOLE</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-category-title">COMMERCE CORE</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="<?= BASE_URL ?>/admin/index.php" class="menu-link <?= $currentAdminPage === 'index.php' ? 'active' : '' ?>">
                            <span class="menu-icon">📊</span>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/admin/products.php" class="menu-link <?= in_array($currentAdminPage, ['products.php', 'product-add.php', 'product-edit.php']) ? 'active' : '' ?>">
                            <span class="menu-icon">👗</span>
                            <span>Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/admin/categories.php" class="menu-link <?= $currentAdminPage === 'categories.php' ? 'active' : '' ?>">
                            <span class="menu-icon">🗂️</span>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/admin/orders.php" class="menu-link <?= in_array($currentAdminPage, ['orders.php', 'order-view.php']) ? 'active' : '' ?>">
                            <span class="menu-icon">📦</span>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/admin/customers.php" class="menu-link <?= $currentAdminPage === 'customers.php' ? 'active' : '' ?>">
                            <span class="menu-icon">👥</span>
                            <span>Customers</span>
                        </a>
                    </li>
                </ul>

                <div class="nav-category-title">MARKETING & BRAND</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="<?= BASE_URL ?>/admin/coupons.php" class="menu-link <?= $currentAdminPage === 'coupons.php' ? 'active' : '' ?>">
                            <span class="menu-icon">🏷️</span>
                            <span>Coupons</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/admin/reviews.php" class="menu-link <?= $currentAdminPage === 'reviews.php' ? 'active' : '' ?>">
                            <span class="menu-icon">★</span>
                            <span>Reviews</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/admin/newsletter.php" class="menu-link <?= $currentAdminPage === 'newsletter.php' ? 'active' : '' ?>">
                            <span class="menu-icon">✉️</span>
                            <span>Newsletter</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/admin/settings.php" class="menu-link <?= $currentAdminPage === 'settings.php' ? 'active' : '' ?>">
                            <span class="menu-icon">⚙️</span>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-bottom">
                <a href="<?= BASE_URL ?>/" target="_blank" class="sidebar-external-link">
                    <span>↗ View Live Boutique</span>
                </a>
                <div class="sidebar-user-block">
                    <span class="user-avatar"><?= strtoupper(substr($adminUser['full_name'], 0, 1)) ?></span>
                    <div class="user-meta">
                        <span class="user-name"><?= e($adminUser['full_name']) ?></span>
                        <a href="<?= BASE_URL ?>/admin/logout.php" class="user-logout">Sign Out</a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-main">
            <!-- Top Admin Bar -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <h2 class="topbar-title"><?= isset($pageHeading) ? e($pageHeading) : 'Overview' ?></h2>
                </div>
                <div class="topbar-right">
                    <span class="topbar-date"><?= date('l, d F Y') ?></span>
                    <a href="<?= BASE_URL ?>/" target="_blank" class="btn btn-outline btn-sm">Preview Store ↗</a>
                </div>
            </header>

            <!-- Admin Body Content Begins -->
            <div class="admin-content-inner">
                <?= render_flash_messages() ?>
