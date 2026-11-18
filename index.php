<?php
/**
 * MAISON ÉLAN - Homepage
 * Haute Couture & Designer Fashion Boutique
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/cart-functions.php';

$pageTitle = "MAISON ÉLAN — Designer Sarees, Dresses & Modern Indian Couture";
$pageDescription = "Discover Maison Élan. Contemporary silhouettes, timeless Banarasi handlooms, sculpted evening dresses, and festive occasion wear. Designed to be remembered.";
$bodyClass = "home-page";

// Fetch 8 New Arrivals for Section 4
$newArrivals = get_products(['is_new' => 1, 'limit' => 8]);
if (count($newArrivals) < 8) {
    // If fewer than 8, get general active products up to 8
    $newArrivals = get_products(['limit' => 8]);
}

// Fetch 5 Bestsellers for Section 6
$bestsellers = get_products(['is_bestseller' => 1, 'limit' => 5]);
if (empty($bestsellers)) {
    $bestsellers = array_slice(get_products(['limit' => 6]), 0, 5);
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- SECTION 1: FULL SCREEN HERO -->
<section class="hero-section" aria-label="Hero Showcase">
    <div class="hero-bg-media">
        <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=2000&q=85" 
             alt="Maison Élan New Season Collection 2026 - Haute Couture" 
             class="hero-bg-img"
             fetchpriority="high">
        <div class="hero-overlay"></div>
    </div>
    
    <div class="hero-content site-container">
        <div class="hero-text-box">
            <span class="hero-tagline">NEW SEASON COLLECTION 2026</span>
            <h1 class="hero-headline">THE ART OF<br><em>DRESSING</em></h1>
            <p class="hero-description">
                Contemporary silhouettes, timeless craftsmanship, and statement pieces designed for unforgettable moments.
            </p>
            <div class="hero-cta-group">
                <a href="<?= BASE_URL ?>/shop.php?filter=new" class="btn btn-hero btn-hero-primary">Shop New Arrivals</a>
                <a href="<?= BASE_URL ?>/shop.php" class="btn btn-hero btn-hero-secondary">Explore Collection</a>
            </div>
        </div>
    </div>

    <!-- Scroll down indicator -->
    <a href="#brand-statement" class="hero-scroll-indicator" aria-label="Scroll to content">
        <span class="scroll-text">DISCOVER</span>
        <span class="scroll-line"></span>
    </a>
</section>


<!-- SECTION 2: BRAND STATEMENT -->
<section class="brand-statement-section" id="brand-statement">
    <div class="site-container statement-inner">
        <span class="editorial-kicker">THE PHILOSOPHY</span>
        <blockquote class="editorial-quote">
            "Fashion is not what you wear.<br>
            <em>It is how you are remembered.</em>"
        </blockquote>
        <p class="statement-subtext">
            Thoughtfully designed pieces for celebrations, evenings and everything worth dressing up for.
        </p>
        <div class="statement-emblem">
            <span class="emblem-line"></span>
            <span class="emblem-icon">❖</span>
            <span class="emblem-line"></span>
        </div>
    </div>
</section>


<!-- SECTION 3: SHOP BY CATEGORY -->
<section class="category-showcase-section">
    <div class="site-container">
        <div class="section-editorial-header">
            <span class="section-eyebrow">CURATED CHAPTERS</span>
            <h2 class="section-title">Shop by Category</h2>
        </div>

        <div class="category-editorial-grid">
            <!-- Card 1: Sarees -->
            <a href="<?= BASE_URL ?>/shop.php?category=sarees" class="category-card">
                <div class="category-card-image-wrap">
                    <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1200&q=80" 
                         alt="Designer Sarees" 
                         class="category-card-img" 
                         loading="lazy">
                    <div class="category-card-overlay"></div>
                </div>
                <div class="category-card-meta">
                    <span class="cat-count">01</span>
                    <h3 class="cat-title">Sarees</h3>
                    <span class="cat-explore">EXPLORE <span>&rarr;</span></span>
                </div>
            </a>

            <!-- Card 2: Dresses -->
            <a href="<?= BASE_URL ?>/shop.php?category=dresses" class="category-card">
                <div class="category-card-image-wrap">
                    <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1200&q=80" 
                         alt="Sculpted Dresses" 
                         class="category-card-img" 
                         loading="lazy">
                    <div class="category-card-overlay"></div>
                </div>
                <div class="category-card-meta">
                    <span class="cat-count">02</span>
                    <h3 class="cat-title">Dresses</h3>
                    <span class="cat-explore">EXPLORE <span>&rarr;</span></span>
                </div>
            </a>

            <!-- Card 3: Occasion Wear -->
            <a href="<?= BASE_URL ?>/shop.php?category=occasion-wear" class="category-card">
                <div class="category-card-image-wrap">
                    <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1200&q=80" 
                         alt="Occasion Wear & Anarkalis" 
                         class="category-card-img" 
                         loading="lazy">
                    <div class="category-card-overlay"></div>
                </div>
                <div class="category-card-meta">
                    <span class="cat-count">03</span>
                    <h3 class="cat-title">Occasion Wear</h3>
                    <span class="cat-explore">EXPLORE <span>&rarr;</span></span>
                </div>
            </a>

            <!-- Card 4: Everyday Luxe -->
            <a href="<?= BASE_URL ?>/shop.php?category=everyday-luxe" class="category-card">
                <div class="category-card-image-wrap">
                    <img src="https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1200&q=80" 
                         alt="Everyday Luxe Co-ords & Separates" 
                         class="category-card-img" 
                         loading="lazy">
                    <div class="category-card-overlay"></div>
                </div>
                <div class="category-card-meta">
                    <span class="cat-count">04</span>
                    <h3 class="cat-title">Everyday Luxe</h3>
                    <span class="cat-explore">EXPLORE <span>&rarr;</span></span>
                </div>
            </a>
        </div>
    </div>
</section>


<!-- SECTION 4: NEW ARRIVALS -->
<section class="new-arrivals-section">
    <div class="site-container">
        <div class="section-editorial-header">
            <span class="section-eyebrow">FRESH FROM THE ATELIER</span>
            <h2 class="section-title">New Arrivals</h2>
            <p class="section-lead">The latest silhouettes crafted in rich silks, fine organzas and artisanal embroideries.</p>
        </div>

        <div class="products-editorial-grid">
            <?php foreach ($newArrivals as $product): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>

        <div class="section-cta-wrap">
            <a href="<?= BASE_URL ?>/shop.php?filter=new" class="btn btn-outline btn-lg">View All New Arrivals</a>
        </div>
    </div>
</section>


<!-- SECTION 5: FEATURED COLLECTION (SPLIT ASYMMETRIC EDITORIAL) -->
<section class="split-collection-section">
    <div class="split-collection-grid">
        <!-- Left Editorial Image -->
        <div class="split-image-col">
            <div class="split-image-wrap">
                <img src="https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=1600&q=85" 
                     alt="The Wedding Edit by Maison Élan" 
                     class="split-img" 
                     loading="lazy">
                <div class="split-badge-overlay">
                    <span>BRIDAL & TROUSSEAU 2026</span>
                </div>
            </div>
        </div>

        <!-- Right Editorial Content -->
        <div class="split-content-col">
            <div class="split-content-inner">
                <span class="editorial-kicker">FEATURED COLLECTION</span>
                <h2 class="split-title">THE WEDDING EDIT</h2>
                <div class="editorial-divider"></div>
                <p class="split-description">
                    "Curated silhouettes for the celebrations that stay with you."
                </p>
                <p class="split-detail">
                    From pure Banarasi katan silks hand-loomed with real gold zari to sculpted velvet kaftans and cascading georgette gowns, each silhouette in The Wedding Edit represents over two hundred hours of painstaking artisan needlework.
                </p>
                <div class="split-features">
                    <div class="split-feature-item">
                        <span class="feature-num">01</span>
                        <span class="feature-text">Heirloom Handloom Silks</span>
                    </div>
                    <div class="split-feature-item">
                        <span class="feature-num">02</span>
                        <span class="feature-text">Authentic Zardozi & Marodi</span>
                    </div>
                    <div class="split-feature-item">
                        <span class="feature-num">03</span>
                        <span class="feature-text">Custom Made-to-Measure Options</span>
                    </div>
                </div>
                <div class="split-actions">
                    <a href="<?= BASE_URL ?>/shop.php?collection=wedding" class="btn btn-primary btn-lg">Shop Wedding Collection</a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- SECTION 6: BESTSELLERS CAROUSEL -->
<section class="bestsellers-section">
    <div class="site-container">
        <div class="bestsellers-header-row">
            <div>
                <span class="section-eyebrow">MOST COVETED SILHOUETTES</span>
                <h2 class="section-title">"THE PIECES EVERYONE IS TALKING ABOUT"</h2>
            </div>
            <div class="carousel-nav-arrows">
                <button type="button" class="carousel-arrow prev" id="bestsellerPrev" aria-label="Previous pieces">&larr;</button>
                <button type="button" class="carousel-arrow next" id="bestsellerNext" aria-label="Next pieces">&rarr;</button>
            </div>
        </div>

        <div class="bestsellers-carousel-wrapper" id="bestsellersCarousel">
            <div class="bestsellers-track">
                <?php foreach ($bestsellers as $product): ?>
                    <div class="carousel-item-wrap">
                        <?php include __DIR__ . '/includes/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>


<!-- SECTION 7: LOOKBOOK (MASONRY & LIGHTBOX) -->
<section class="lookbook-section">
    <div class="site-container">
        <div class="section-editorial-header">
            <span class="section-eyebrow">EDITORIAL ARCHIVE</span>
            <h2 class="section-title">The Maison Élan Lookbook</h2>
            <p class="section-lead">A visual journey through light, shadow, and architectural drape.</p>
        </div>

        <div class="lookbook-masonry-grid">
            <!-- Look 1 -->
            <div class="lookbook-item item-tall" data-look-id="1" data-look-title="LOOK 01 — Ivory Mulberry Silk Drape" data-img-src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1200&q=85">
                <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=900&q=80" alt="Look 01" loading="lazy">
                <div class="lookbook-hover-overlay">
                    <span class="look-num">LOOK 01</span>
                    <span class="look-link">VIEW LOOK &rarr;</span>
                </div>
            </div>

            <!-- Look 2 -->
            <div class="lookbook-item item-square" data-look-id="2" data-look-title="LOOK 02 — Midnight 32-Kali Flare" data-img-src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1200&q=85">
                <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=900&q=80" alt="Look 02" loading="lazy">
                <div class="lookbook-hover-overlay">
                    <span class="look-num">LOOK 02</span>
                    <span class="look-link">VIEW LOOK &rarr;</span>
                </div>
            </div>

            <!-- Look 3 -->
            <div class="lookbook-item item-tall" data-look-id="3" data-look-title="LOOK 03 — Rose Gold Gossamer Weave" data-img-src="https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1200&q=85">
                <img src="https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=900&q=80" alt="Look 03" loading="lazy">
                <div class="lookbook-hover-overlay">
                    <span class="look-num">LOOK 03</span>
                    <span class="look-link">VIEW LOOK &rarr;</span>
                </div>
            </div>

            <!-- Look 4 -->
            <div class="lookbook-item item-wide" data-look-id="4" data-look-title="LOOK 04 — Asymmetric Cowl Drama" data-img-src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1200&q=85">
                <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=900&q=80" alt="Look 04" loading="lazy">
                <div class="lookbook-hover-overlay">
                    <span class="look-num">LOOK 04</span>
                    <span class="look-link">VIEW LOOK &rarr;</span>
                </div>
            </div>

            <!-- Look 5 -->
            <div class="lookbook-item item-square" data-look-id="5" data-look-title="LOOK 05 — Tailored Emerald Crepe" data-img-src="https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=1200&q=85">
                <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=900&q=80" alt="Look 05" loading="lazy">
                <div class="lookbook-hover-overlay">
                    <span class="look-num">LOOK 05</span>
                    <span class="look-link">VIEW LOOK &rarr;</span>
                </div>
            </div>

            <!-- Look 6 -->
            <div class="lookbook-item item-tall" data-look-id="6" data-look-title="LOOK 06 — Heritage Banarasi Katan Silk" data-img-src="https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=1200&q=85">
                <img src="https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=900&q=80" alt="Look 06" loading="lazy">
                <div class="lookbook-hover-overlay">
                    <span class="look-num">LOOK 06</span>
                    <span class="look-link">VIEW LOOK &rarr;</span>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- SECTION 8: WHY MAISON ÉLAN -->
<section class="why-us-section">
    <div class="site-container">
        <div class="why-us-grid">
            <!-- 1. Handpicked Design -->
            <div class="why-feature-card">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </div>
                <h3 class="feature-title">HANDPICKED DESIGN</h3>
                <p class="feature-text">
                    Exclusive collections produced in limited ateliers. Each silhouette is individually drafted and refined to ensure distinct modern poise.
                </p>
            </div>

            <!-- 2. Quality Fabrics -->
            <div class="why-feature-card">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
                <h3 class="feature-title">QUALITY FABRICS</h3>
                <p class="feature-text">
                    Pure mulberry silks, handspun Chanderis, double-faced satins, and gossamer organzas sourced directly from certified heritage weaver clusters.
                </p>
            </div>

            <!-- 3. Crafted With Detail -->
            <div class="why-feature-card">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3 class="feature-title">CRAFTED WITH DETAIL</h3>
                <p class="feature-text">
                    Hand-guided French seams, interior lining in breathable habotai, and authentic metal zardozi embroidery executed by multi-generational karigars.
                </p>
            </div>

            <!-- 4. Delivered With Care -->
            <div class="why-feature-card">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                </div>
                <h3 class="feature-title">DELIVERED WITH CARE</h3>
                <p class="feature-text">
                    Signature luxury gift packaging with tamper-evident golden seals, insured express courier, and dedicated boutique styling concierge support.
                </p>
            </div>
        </div>
    </div>
</section>


<!-- SECTION 9: CUSTOMER REVIEWS -->
<section class="testimonials-section">
    <div class="site-container">
        <div class="section-editorial-header">
            <span class="section-eyebrow">CLIENT PATRONAGE</span>
            <h2 class="section-title">Words from Our Patrons</h2>
        </div>

        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-quote">
                    "Every detail felt luxurious, from the packaging to the outfit itself. The bespoke drape of the ivory saree is truly unmatched."
                </p>
                <div class="testimonial-author">
                    <span class="author-name">Devika Malhotra</span>
                    <span class="author-location">New Delhi</span>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-quote">
                    "Absolutely loved the fit and finish. The saree looked even better in person. The craftsmanship on the organza scalloped border is breathtaking."
                </p>
                <div class="testimonial-author">
                    <span class="author-name">Ananya Roy Chowdhury</span>
                    <span class="author-location">Kolkata</span>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-quote">
                    "One of the best boutique shopping experiences I've had. Ordered the Midnight Anarkali for a reception and received non-stop compliments."
                </p>
                <div class="testimonial-author">
                    <span class="author-name">Radhika Merchant</span>
                    <span class="author-location">Mumbai</span>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- SECTION 10: INSTAGRAM SECTION -->
<section class="instagram-section">
    <div class="site-container">
        <div class="instagram-header">
            <span class="section-eyebrow">THE DIGITAL SALON</span>
            <h2 class="section-title">FOLLOW THE ÉLAN</h2>
            <p class="instagram-subtitle">Style, stories and new arrivals.</p>
            <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" class="instagram-handle">@maisonelan</a>
        </div>
    </div>

    <div class="instagram-grid">
        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" class="instagram-card">
            <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=600&q=80" alt="@maisonelan editorial" loading="lazy">
            <div class="insta-overlay"><span class="insta-icon">📷</span></div>
        </a>
        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" class="instagram-card">
            <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=600&q=80" alt="@maisonelan editorial" loading="lazy">
            <div class="insta-overlay"><span class="insta-icon">📷</span></div>
        </a>
        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" class="instagram-card">
            <img src="https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=600&q=80" alt="@maisonelan editorial" loading="lazy">
            <div class="insta-overlay"><span class="insta-icon">📷</span></div>
        </a>
        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" class="instagram-card">
            <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=600&q=80" alt="@maisonelan editorial" loading="lazy">
            <div class="insta-overlay"><span class="insta-icon">📷</span></div>
        </a>
        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" class="instagram-card">
            <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=600&q=80" alt="@maisonelan editorial" loading="lazy">
            <div class="insta-overlay"><span class="insta-icon">📷</span></div>
        </a>
        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" class="instagram-card">
            <img src="https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=600&q=80" alt="@maisonelan editorial" loading="lazy">
            <div class="insta-overlay"><span class="insta-icon">📷</span></div>
        </a>
    </div>
</section>


<!-- SECTION 11: NEWSLETTER SECTION -->
<section class="newsletter-section">
    <div class="site-container">
        <div class="newsletter-box">
            <span class="section-eyebrow">PRIVATE PRIVILEGES</span>
            <h2 class="newsletter-headline">BE THE FIRST TO KNOW</h2>
            <p class="newsletter-subheadline">
                Get early access to new collections, private launches and exclusive offers.
            </p>
            
            <form class="newsletter-main-form" id="homeNewsletterForm">
                <div class="newsletter-form-row">
                    <input type="email" 
                           name="email" 
                           class="newsletter-input-field" 
                           placeholder="Enter your email address..." 
                           required>
                    <button type="submit" class="btn btn-primary newsletter-submit-btn">JOIN THE LIST</button>
                </div>
                <div class="newsletter-response" id="homeNewsletterResponse"></div>
            </form>
            <p class="newsletter-disclaimer">By subscribing you agree to our <a href="<?= BASE_URL ?>/privacy-policy.php">Privacy Policy</a>. Unsubscribe at any time.</p>
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
