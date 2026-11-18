<?php
/**
 * MAISON ÉLAN - Header Component
 * Luxury Editorial Navigation & Modals
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/cart-functions.php';

$cartCount = get_cart_count();
$wishlistCount = count(get_wishlist_ids());
$isLoggedIn = is_logged_in();
$currentUser = get_logged_in_user();
$currentPage = basename($_SERVER['PHP_SELF']);

$pageTitle = isset($pageTitle) ? $pageTitle . " — " . SITE_NAME : SITE_NAME . " — " . SITE_TAGLINE;
$pageDescription = isset($pageDescription) ? $pageDescription : SITE_DESCRIPTION;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <link rel="canonical" href="<?= e(BASE_URL . $_SERVER['REQUEST_URI']) ?>">

    <!-- Open Graph / Meta -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:url" content="<?= e(BASE_URL . $_SERVER['REQUEST_URI']) ?>">
    <meta property="og:image" content="<?= isset($ogImage) ? e($ogImage) : 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1200&q=80' ?>">

    <!-- Google Typography: Cormorant Garamond & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css?v=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/responsive.css?v=1.0">

    <!-- Global JS Config -->
    <script>
        window.MAISON = {
            baseUrl: '<?= BASE_URL ?>',
            currency: '<?= CURRENCY_SYMBOL ?>',
            csrfToken: '<?= csrf_token() ?>'
        };
    </script>
</head>
<body class="<?= isset($bodyClass) ? e($bodyClass) : '' ?>">

    <!-- Top Announcement Bar -->
    <aside class="announcement-bar" role="region" aria-label="Announcement">
        <div class="site-container announcement-content">
            <span class="announcement-text">Complimentary White-Glove Shipping on Orders Above ₹5,000 | Bespoke Atelier Enquiries Open</span>
            <div class="announcement-links">
                <a href="<?= BASE_URL ?>/contact.php" class="announcement-link">Concierge</a>
                <span class="divider">|</span>
                <a href="<?= BASE_URL ?>/orders.php" class="announcement-link">Track Order</a>
            </div>
        </div>
    </aside>

    <!-- Main Sticky Header -->
    <header id="site-header" class="site-header">
        <div class="header-inner site-container">
            
            <!-- Mobile Menu Toggle Button -->
            <button type="button" class="mobile-toggle-btn" id="mobileMenuOpen" aria-label="Open Navigation Menu">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>

            <!-- Brand Logo -->
            <div class="brand-logo-wrap">
                <a href="<?= BASE_URL ?>/" class="brand-logo">
                    <span class="logo-title">MAISON ÉLAN</span>
                    <span class="logo-subtitle">HAUTE COUTURE • NEW DELHI</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="desktop-navigation" aria-label="Primary Navigation">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/shop.php?filter=new" class="nav-link <?= (isset($_GET['filter']) && $_GET['filter'] === 'new') ? 'active' : '' ?>">New Arrivals</a>
                    </li>
                    <li class="nav-item nav-has-dropdown">
                        <a href="<?= BASE_URL ?>/shop.php" class="nav-link <?= ($currentPage === 'shop.php' && empty($_GET['category'])) ? 'active' : '' ?>">
                            Collections <span class="nav-chevron">▾</span>
                        </a>
                        <div class="nav-dropdown">
                            <ul class="dropdown-list">
                                <li><a href="<?= BASE_URL ?>/shop.php?collection=wedding">The Wedding Edit 2026</a></li>
                                <li><a href="<?= BASE_URL ?>/shop.php?category=sarees">Heritage Banarasi & Silks</a></li>
                                <li><a href="<?= BASE_URL ?>/shop.php?category=occasion-wear">Festive Soiree Ensembles</a></li>
                                <li><a href="<?= BASE_URL ?>/shop.php?category=everyday-luxe">Everyday Silk Separates</a></li>
                                <li><a href="<?= BASE_URL ?>/lookbook.php">Atelier Lookbook</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/shop.php?category=sarees" class="nav-link <?= (isset($_GET['category']) && $_GET['category'] === 'sarees') ? 'active' : '' ?>">Sarees</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/shop.php?category=dresses" class="nav-link <?= (isset($_GET['category']) && $_GET['category'] === 'dresses') ? 'active' : '' ?>">Dresses</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/shop.php?category=kurtis" class="nav-link <?= (isset($_GET['category']) && $_GET['category'] === 'kurtis') ? 'active' : '' ?>">Kurtis</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/shop.php?category=occasion-wear" class="nav-link <?= (isset($_GET['category']) && $_GET['category'] === 'occasion-wear') ? 'active' : '' ?>">Occasion Wear</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/about.php" class="nav-link <?= ($currentPage === 'about.php') ? 'active' : '' ?>">About</a>
                    </li>
                </ul>
            </nav>

            <!-- Header Right Icons -->
            <div class="header-utilities">
                <!-- Search Trigger -->
                <button type="button" class="util-btn" id="searchModalTrigger" aria-label="Search Boutique" title="Search">
                    <svg class="util-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>

                <!-- Customer Account -->
                <div class="account-menu-wrap">
                    <a href="<?= $isLoggedIn ? BASE_URL . '/account.php' : BASE_URL . '/login.php' ?>" class="util-btn" aria-label="My Account" title="<?= $isLoggedIn ? 'Account: ' . e($currentUser['full_name'] ?? '') : 'Sign In' ?>">
                        <svg class="util-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                </div>

                <!-- Wishlist -->
                <a href="<?= BASE_URL ?>/wishlist.php" class="util-btn util-wishlist" aria-label="My Wishlist" title="Wishlist">
                    <svg class="util-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <span class="util-badge wishlist-badge" id="wishlistCountBadge" style="<?= $wishlistCount > 0 ? '' : 'display:none;' ?>"><?= $wishlistCount ?></span>
                </a>

                <!-- Shopping Bag -->
                <a href="<?= BASE_URL ?>/cart.php" class="util-btn util-cart" id="openCartDrawer" aria-label="Shopping Bag" title="Shopping Bag">
                    <svg class="util-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <span class="util-badge cart-badge" id="cartCountBadge" style="<?= $cartCount > 0 ? '' : 'display:none;' ?>"><?= $cartCount ?></span>
                </a>
            </div>

        </div>
    </header>

    <!-- Mobile Slide-Out Drawer -->
    <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="mobile-drawer-header">
            <span class="mobile-drawer-brand">MAISON ÉLAN</span>
            <button type="button" class="mobile-drawer-close" id="mobileMenuClose" aria-label="Close menu">&times;</button>
        </div>
        <div class="mobile-drawer-content">
            <ul class="mobile-nav-list">
                <li><a href="<?= BASE_URL ?>/" class="mobile-nav-link">Home</a></li>
                <li><a href="<?= BASE_URL ?>/shop.php?filter=new" class="mobile-nav-link">New Arrivals 2026</a></li>
                <li><a href="<?= BASE_URL ?>/shop.php?category=sarees" class="mobile-nav-link">Sarees & Silks</a></li>
                <li><a href="<?= BASE_URL ?>/shop.php?category=dresses" class="mobile-nav-link">Designer Dresses</a></li>
                <li><a href="<?= BASE_URL ?>/shop.php?category=kurtis" class="mobile-nav-link">Kurti Sets & Chanderi</a></li>
                <li><a href="<?= BASE_URL ?>/shop.php?category=occasion-wear" class="mobile-nav-link">Occasion & Anarkalis</a></li>
                <li><a href="<?= BASE_URL ?>/shop.php?category=everyday-luxe" class="mobile-nav-link">Everyday Luxe Co-ords</a></li>
                <li><a href="<?= BASE_URL ?>/shop.php?collection=wedding" class="mobile-nav-link">The Wedding Edit</a></li>
                <li><a href="<?= BASE_URL ?>/lookbook.php" class="mobile-nav-link">Editorial Lookbook</a></li>
                <li><a href="<?= BASE_URL ?>/about.php" class="mobile-nav-link">About The Atelier</a></li>
                <li><a href="<?= BASE_URL ?>/contact.php" class="mobile-nav-link">Contact & Concierge</a></li>
            </ul>

            <div class="mobile-drawer-footer">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= BASE_URL ?>/account.php" class="mobile-footer-btn">My Account (<?= e($currentUser['full_name'] ?? 'User') ?>)</a>
                    <a href="<?= BASE_URL ?>/orders.php" class="mobile-footer-btn">My Orders</a>
                    <a href="<?= BASE_URL ?>/logout.php" class="mobile-footer-link">Sign Out</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="btn btn-primary btn-block">Sign In / Register</a>
                <?php endif; ?>
                
                <div class="mobile-boutique-info">
                    <p>Concierge: <?= STORE_PHONE ?></p>
                    <p><?= STORE_HOURS ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop & Mobile Search Overlay -->
    <div class="search-overlay" id="searchOverlay" aria-modal="true" role="dialog">
        <div class="search-overlay-inner">
            <div class="search-overlay-header site-container">
                <span class="search-title">SEARCH THE BOUTIQUE</span>
                <button type="button" class="search-close-btn" id="searchModalClose" aria-label="Close search">&times;</button>
            </div>
            <div class="search-form-wrap site-container">
                <form action="<?= BASE_URL ?>/shop.php" method="GET" class="search-overlay-form" id="searchLiveForm">
                    <div class="search-input-group">
                        <input type="text" 
                               name="search" 
                               id="liveSearchInput" 
                               class="search-input" 
                               placeholder="Search for sarees, dresses, anarkalis, or co-ord sets..." 
                               autocomplete="off" 
                               autofocus>
                        <button type="submit" class="search-submit-btn">Search</button>
                    </div>
                </form>

                <!-- Popular Search Suggestions -->
                <div class="search-suggestions">
                    <span class="suggestion-label">Suggested:</span>
                    <a href="<?= BASE_URL ?>/shop.php?category=sarees">Silk Sarees</a>
                    <a href="<?= BASE_URL ?>/shop.php?search=Anarkali">Midnight Anarkali</a>
                    <a href="<?= BASE_URL ?>/shop.php?search=Organza">Rose Gold Organza</a>
                    <a href="<?= BASE_URL ?>/shop.php?collection=wedding">Wedding Edit</a>
                    <a href="<?= BASE_URL ?>/shop.php?search=Co-ord">Festive Co-ord</a>
                </div>

                <!-- Live Search Results Dropdown -->
                <div class="live-search-results" id="liveSearchResults" style="display:none;"></div>
            </div>
        </div>
    </div>

    <!-- Quick View Modal Container -->
    <div class="quickview-modal-backdrop" id="quickViewBackdrop">
        <div class="quickview-modal-content" id="quickViewContent">
            <button type="button" class="quickview-close-btn" id="quickViewClose" aria-label="Close modal">&times;</button>
            <div class="quickview-body" id="quickViewBody">
                <!-- Dynamically populated via api/quickview.php -->
                <div class="quickview-loading">
                    <div class="luxury-spinner"></div>
                    <span>Loading Maison Élan piece...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="toast-container" id="toastContainer" aria-live="polite"></div>

    <!-- Flash message render -->
    <?= render_flash_messages() ?>

    <!-- Main Page Content Wrap Begins -->
    <main id="main-content" class="main-content">
