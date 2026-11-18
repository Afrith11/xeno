<?php
/**
 * MAISON ÉLAN - Editorial Lookbook
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "Editorial Lookbook 2026 — Maison Élan Haute Couture";
$pageDescription = "Explore the Haute Couture 2026 Lookbook. A study of architectural drape, pure silk Banarasi jaals, and sculpted evening silhouettes.";

require_once __DIR__ . '/includes/header.php';
?>

<section class="shop-header-banner">
    <div class="site-container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">Editorial Lookbook</span>
        </nav>
        <span class="editorial-kicker">HAUTE COUTURE ARCHIVE</span>
        <h1 class="shop-page-title">EDITORIAL LOOKBOOK</h1>
        <p class="shop-page-lead">A visual exploration of form, light, and timeless Indian atelier craftsmanship.</p>
    </div>
</section>

<section class="lookbook-gallery-section site-container">
    <div class="lookbook-masonry-grid">
        <!-- Look 01 -->
        <div class="lookbook-item item-tall" data-look-id="1" data-look-title="LOOK 01 — Ivory Silk Draped Saree" data-img-src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1400&q=85">
            <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=900&q=80" alt="Look 01" loading="lazy">
            <div class="lookbook-hover-overlay">
                <span class="look-num">LOOK 01</span>
                <span class="look-link">VIEW SILHOUETTE &rarr;</span>
            </div>
        </div>

        <!-- Look 02 -->
        <div class="lookbook-item item-square" data-look-id="2" data-look-title="LOOK 02 — Midnight Embroidered Anarkali" data-img-src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1400&q=85">
            <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=900&q=80" alt="Look 02" loading="lazy">
            <div class="lookbook-hover-overlay">
                <span class="look-num">LOOK 02</span>
                <span class="look-link">VIEW SILHOUETTE &rarr;</span>
            </div>
        </div>

        <!-- Look 03 -->
        <div class="lookbook-item item-tall" data-look-id="3" data-look-title="LOOK 03 — Rose Gold Organza Saree" data-img-src="https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1400&q=85">
            <img src="https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=900&q=80" alt="Look 03" loading="lazy">
            <div class="lookbook-hover-overlay">
                <span class="look-num">LOOK 03</span>
                <span class="look-link">VIEW SILHOUETTE &rarr;</span>
            </div>
        </div>

        <!-- Look 04 -->
        <div class="lookbook-item item-wide" data-look-id="4" data-look-title="LOOK 04 — Burgundy Japanese Satin Evening Gown" data-img-src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1400&q=85">
            <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=900&q=80" alt="Look 04" loading="lazy">
            <div class="lookbook-hover-overlay">
                <span class="look-num">LOOK 04</span>
                <span class="look-link">VIEW SILHOUETTE &rarr;</span>
            </div>
        </div>

        <!-- Look 05 -->
        <div class="lookbook-item item-square" data-look-id="5" data-look-title="LOOK 05 — Emerald Bullion Crepe Co-ord" data-img-src="https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=1400&q=85">
            <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=900&q=80" alt="Look 05" loading="lazy">
            <div class="lookbook-hover-overlay">
                <span class="look-num">LOOK 05</span>
                <span class="look-link">VIEW SILHOUETTE &rarr;</span>
            </div>
        </div>

        <!-- Look 06 -->
        <div class="lookbook-item item-tall" data-look-id="6" data-look-title="LOOK 06 — Crimson Banarasi Katan Heirloom" data-img-src="https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=1400&q=85">
            <img src="https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=900&q=80" alt="Look 06" loading="lazy">
            <div class="lookbook-hover-overlay">
                <span class="look-num">LOOK 06</span>
                <span class="look-link">VIEW SILHOUETTE &rarr;</span>
            </div>
        </div>
    </div>
</section>

<!-- Lightbox Modal for Lookbook -->
<div class="lookbook-lightbox" id="lookbookLightbox" aria-hidden="true">
    <button type="button" class="lightbox-close" id="lightboxClose">&times;</button>
    <div class="lightbox-content">
        <img src="" alt="" id="lightboxImg" class="lightbox-img">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
