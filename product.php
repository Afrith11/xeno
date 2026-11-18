<?php
/**
 * MAISON ÉLAN - Product Detail Page
 * Gallery, Variants, Accordions, Reviews & JSON-LD
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/cart-functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

$product = null;
if (!empty($slug)) {
    $product = get_product($slug);
} elseif ($id > 0) {
    $product = get_product($id);
}

// Fallback to first product if none specified
if (!$product) {
    $all = get_products(['limit' => 1]);
    $product = $all[0] ?? null;
}

if (!$product) {
    redirect(BASE_URL . '/shop.php');
}

// Pricing
$price = (float)$product['price'];
$salePrice = !empty($product['sale_price']) && $product['sale_price'] > 0 ? (float)$product['sale_price'] : null;
$effectivePrice = $salePrice ?: $price;
$oldPrice = $salePrice ? $price : null;
$discountPercent = $oldPrice ? calculate_discount_percent($oldPrice, $effectivePrice) : 0;

// Sizes & Colors
$sizes = array_map('trim', explode(',', $product['sizes'] ?? 'Free Size'));
$colors = array_map('trim', explode(',', $product['colors'] ?? 'Original'));

// Images
$images = $product['images'] ?? [];
if (empty($images)) {
    $images = [
        ['image_url' => $product['primary_image'] ?? 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1200&q=80', 'alt_text' => $product['name']],
        ['image_url' => $product['hover_image'] ?? 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1200&q=80', 'alt_text' => $product['name'] . ' Detail']
    ];
}

// Related products
$relatedProducts = get_products([
    'category_id' => $product['category_id'] ?? null,
    'limit' => 4
]);
// Filter out current product
$relatedProducts = array_filter($relatedProducts, fn($p) => (int)$p['id'] !== (int)$product['id']);
if (count($relatedProducts) < 4) {
    $extra = get_products(['limit' => 5]);
    foreach ($extra as $ex) {
        if ((int)$ex['id'] !== (int)$product['id'] && count($relatedProducts) < 4) {
            $relatedProducts[] = $ex;
        }
    }
}

// Customer reviews
$reviews = get_product_reviews((int)$product['id']);

// Page SEO Meta
$pageTitle = $product['name'] . " — Designer Fashion Boutique";
$pageDescription = $product['short_description'];
$ogImage = $images[0]['image_url'] ?? '';

// Wishlist check
$isWishlisted = in_array((int)$product['id'], get_wishlist_ids());

require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<div class="product-breadcrumb-wrap site-container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= BASE_URL ?>/">Home</a>
        <span class="crumb-sep">/</span>
        <a href="<?= BASE_URL ?>/shop.php?category=<?= urlencode($product['category_slug'] ?? '') ?>"><?= e($product['category_name'] ?? 'Catalog') ?></a>
        <span class="crumb-sep">/</span>
        <span class="crumb-current"><?= e($product['name']) ?></span>
    </nav>
</div>

<!-- Product Detail Hero -->
<section class="product-detail-section site-container">
    <div class="product-detail-grid">
        
        <!-- Left: Image Gallery -->
        <div class="product-gallery">
            <!-- Thumbnail list -->
            <div class="gallery-thumbs">
                <?php foreach ($images as $index => $img): ?>
                    <button type="button" 
                            class="gallery-thumb-btn <?= $index === 0 ? 'active' : '' ?>" 
                            data-img-src="<?= e($img['image_url']) ?>" 
                            aria-label="View angle <?= $index + 1 ?>">
                        <img src="<?= e($img['image_url']) ?>" alt="Thumbnail <?= $index + 1 ?>">
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Main Large Image Stage -->
            <div class="gallery-stage">
                <?php if ($discountPercent > 0): ?>
                    <span class="badge badge-sale gallery-badge">-<?= $discountPercent ?>%</span>
                <?php endif; ?>
                <div class="gallery-stage-zoom" id="galleryStageZoom">
                    <img src="<?= e($images[0]['image_url']) ?>" 
                         alt="<?= e($product['name']) ?>" 
                         id="galleryMainImg" 
                         class="gallery-main-img" 
                         fetchpriority="high">
                </div>
            </div>
        </div>

        <!-- Right: Product Purchase Information -->
        <div class="product-info-col">
            <span class="product-atelier-tag">MAISON ÉLAN ATELIER • SKU: <?= e($product['sku']) ?></span>
            <h1 class="product-page-title"><?= e($product['name']) ?></h1>

            <!-- Ratings -->
            <div class="product-rating-row">
                <?= render_rating_stars((float)$product['rating'], count($reviews) ?: (int)$product['reviews_count']) ?>
                <a href="#customer-reviews" class="reviews-anchor-link">Read Reviews (<?= count($reviews) ?: (int)$product['reviews_count'] ?>)</a>
            </div>

            <!-- Price Breakdown -->
            <div class="product-price-block">
                <span class="product-price-effective"><?= format_price($effectivePrice) ?></span>
                <?php if ($oldPrice): ?>
                    <span class="product-price-regular"><?= format_price($oldPrice) ?></span>
                    <span class="product-discount-label">Save <?= format_price($oldPrice - $effectivePrice) ?> (<?= $discountPercent ?>% Off)</span>
                <?php endif; ?>
                <span class="tax-shipping-note">Inclusive of all taxes. Free insured boutique delivery.</span>
            </div>

            <div class="product-short-desc">
                <p><?= e($product['short_description']) ?></p>
            </div>

            <!-- Add to Cart / Buy Now Form -->
            <form id="productPurchaseForm" class="product-purchase-form" action="<?= BASE_URL ?>/cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <?= csrf_field() ?>

                <!-- Color Selection -->
                <?php if (!empty($colors) && count($colors) > 0 && $colors[0] !== ''): ?>
                    <div class="variant-group">
                        <label class="variant-label">
                            <span>Color Palette:</span>
                            <strong class="selected-color-name" id="selectedColorLabel"><?= e($colors[0]) ?></strong>
                        </label>
                        <div class="color-swatches-row">
                            <?php foreach ($colors as $index => $color): ?>
                                <label class="color-swatch-item">
                                    <input type="radio" 
                                           name="color" 
                                           value="<?= e($color) ?>" 
                                           <?= $index === 0 ? 'checked' : '' ?>
                                           onchange="document.getElementById('selectedColorLabel').innerText = this.value">
                                    <span class="color-swatch-chip"><?= e($color) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Size Selection -->
                <div class="variant-group">
                    <div class="variant-label-row">
                        <label class="variant-label">
                            <span>Select Size:</span>
                            <strong class="selected-size-name" id="selectedSizeLabel"><?= e($sizes[0]) ?></strong>
                        </label>
                        <button type="button" class="size-guide-trigger" id="openSizeGuideModalBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14">
                                <rect x="2" y="7" width="20" height="10" rx="1"></rect>
                                <line x1="6" y1="7" x2="6" y2="11"></line>
                                <line x1="10" y1="7" x2="10" y2="13"></line>
                                <line x1="14" y1="7" x2="14" y2="11"></line>
                                <line x1="18" y1="7" x2="18" y2="13"></line>
                            </svg>
                            Size Guide & Measurements
                        </button>
                    </div>

                    <div class="size-buttons-row">
                        <?php foreach ($sizes as $index => $sz): ?>
                            <label class="size-btn-item">
                                <input type="radio" 
                                       name="size" 
                                       value="<?= e($sz) ?>" 
                                       <?= $index === 0 ? 'checked' : '' ?> 
                                       onchange="document.getElementById('selectedSizeLabel').innerText = this.value">
                                <span class="size-btn-label"><?= e($sz) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quantity & Actions -->
                <div class="purchase-actions-row">
                    <div class="quantity-stepper">
                        <button type="button" class="qty-btn minus" id="pdpQtyMinus" aria-label="Decrease quantity">−</button>
                        <input type="number" name="quantity" id="pdpQtyInput" class="qty-input" value="1" min="1" max="<?= (int)$product['stock_quantity'] ?>">
                        <button type="button" class="qty-btn plus" id="pdpQtyPlus" aria-label="Increase quantity">+</button>
                    </div>

                    <button type="button" class="btn btn-primary btn-add-bag-pdp" id="btnAddToBagPdp">
                        <span>ADD TO SHOPPING BAG</span>
                    </button>

                    <button type="button" 
                            class="btn-wishlist-pdp <?= $isWishlisted ? 'active' : '' ?>" 
                            id="pdpWishlistToggle" 
                            data-product-id="<?= (int)$product['id'] ?>"
                            aria-label="Save to Wishlist" 
                            title="Save to Wishlist">
                        <svg class="heart-icon" viewBox="0 0 24 24" fill="<?= $isWishlisted ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="1.6">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </button>
                </div>

                <div class="buy-now-wrap">
                    <button type="button" class="btn btn-buy-now btn-block" id="btnBuyNowPdp">
                        BUY IT NOW
                    </button>
                </div>
            </form>

            <!-- Stock and Concierge Status -->
            <div class="stock-status-notice">
                <span class="stock-dot"></span>
                <span>In Stock at Delhi Atelier. Dispatches within 24-48 business hours.</span>
            </div>

            <!-- Accordion Details -->
            <div class="product-accordions">
                <!-- 1. Product Description -->
                <div class="accordion-item active">
                    <button type="button" class="accordion-header" aria-expanded="true">
                        <span>Product Details & Silhouette</span>
                        <span class="acc-icon">+</span>
                    </button>
                    <div class="accordion-panel">
                        <div class="panel-content">
                            <p><?= nl2br(e($product['description'])) ?></p>
                            <ul class="panel-bullet-list">
                                <li><strong>Craftsmanship:</strong> Artisanal handloom & precision finish</li>
                                <li><strong>Silhouette:</strong> Designed for poised occasion & gala presence</li>
                                <li><strong>Lining:</strong> Premium tonal soft lining</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 2. Fabric & Care -->
                <div class="accordion-item">
                    <button type="button" class="accordion-header" aria-expanded="false">
                        <span>Fabric & Atelier Care</span>
                        <span class="acc-icon">+</span>
                    </button>
                    <div class="accordion-panel">
                        <div class="panel-content">
                            <p><?= !empty($product['fabric_care']) ? nl2br(e($product['fabric_care'])) : 'Dry clean only by certified luxury garment specialists. Wrap in unbleached pure cotton or muslin when stored. Avoid spraying alcohols or perfume directly on fine metallic threads.' ?></p>
                        </div>
                    </div>
                </div>

                <!-- 3. Shipping Information -->
                <div class="accordion-item">
                    <button type="button" class="accordion-header" aria-expanded="false">
                        <span>Shipping & Delivery</span>
                        <span class="acc-icon">+</span>
                    </button>
                    <div class="accordion-panel">
                        <div class="panel-content">
                            <p>We provide complimentary insured white-glove shipping across India on orders exceeding ₹5,000. All pieces are packed in signature Maison Élan gold-embossed keepsake garment boxes. Metro city deliveries arrive within 3-4 business days.</p>
                        </div>
                    </div>
                </div>

                <!-- 4. Returns & Exchanges -->
                <div class="accordion-item">
                    <button type="button" class="accordion-header" aria-expanded="false">
                        <span>Returns & Exchanges</span>
                        <span class="acc-icon">+</span>
                    </button>
                    <div class="accordion-panel">
                        <div class="panel-content">
                            <p>We offer seamless 7-day returns and size exchanges. Garments must be unworn, unwashed, and returned with the signature boutique security seal attached. Made-to-measure bespoke couture is eligible for complimentary atelier alterations.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Customer Reviews Section -->
<section class="reviews-section site-container" id="customer-reviews">
    <div class="reviews-container">
        <div class="section-editorial-header">
            <span class="section-eyebrow">CLIENT TESTIMONIALS</span>
            <h2 class="section-title">Reviews & Impressions</h2>
        </div>

        <div class="reviews-layout">
            <!-- Review Summary Breakdown -->
            <div class="reviews-summary-card">
                <div class="overall-score"><?= number_format((float)$product['rating'], 1) ?></div>
                <div class="overall-stars"><?= render_rating_stars((float)$product['rating']) ?></div>
                <p class="score-caption">Based on <?= count($reviews) ?: (int)$product['reviews_count'] ?> verified client reviews</p>
                <div class="score-recommendation">100% of verified patrons recommend this silhouette</div>
            </div>

            <!-- Reviews List -->
            <div class="reviews-feed">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="review-entry">
                            <div class="review-entry-header">
                                <div class="review-stars-small">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <span class="star"><?= $s <= (int)$rev['rating'] ? '★' : '☆' ?></span>
                                    <?php endfor; ?>
                                </div>
                                <span class="review-verified-badge">✓ Verified Patron</span>
                            </div>
                            <?php if (!empty($rev['title'])): ?>
                                <h4 class="review-title"><?= e($rev['title']) ?></h4>
                            <?php endif; ?>
                            <p class="review-body">“<?= e($rev['comment']) ?>”</p>
                            <div class="review-author-meta">
                                <span class="author-name"><?= e($rev['customer_name']) ?></span>
                                <?php if (!empty($rev['customer_city'])): ?>
                                    <span class="author-city">— <?= e($rev['customer_city']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-reviews-lead">Be the first to share your experience with this bespoke silhouette.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Related Products "You May Also Like" -->
<section class="related-products-section site-container">
    <div class="section-editorial-header">
        <span class="section-eyebrow">CURATED PAIRINGS</span>
        <h2 class="section-title">You May Also Like</h2>
    </div>

    <div class="products-editorial-grid">
        <?php foreach ($relatedProducts as $product): ?>
            <?php include __DIR__ . '/includes/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<!-- JSON-LD Structured Data for SEO / Google Shopping -->
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": <?= json_encode($product['name']) ?>,
  "image": <?= json_encode($images[0]['image_url'] ?? '') ?>,
  "description": <?= json_encode($product['short_description']) ?>,
  "sku": <?= json_encode($product['sku']) ?>,
  "brand": {
    "@type": "Brand",
    "name": "MAISON ÉLAN"
  },
  "offers": {
    "@type": "Offer",
    "url": <?= json_encode(BASE_URL . '/product.php?slug=' . urlencode($product['slug'])) ?>,
    "priceCurrency": "INR",
    "price": <?= json_encode((string)$effectivePrice) ?>,
    "availability": "https://schema.org/InStock",
    "itemCondition": "https://schema.org/NewCondition"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": <?= json_encode((string)$product['rating']) ?>,
    "reviewCount": <?= json_encode((string)(count($reviews) ?: 1)) ?>
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
