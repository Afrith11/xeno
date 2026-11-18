<?php
/**
 * MAISON ÉLAN - Editorial Product Card Component
 * Reusable across Homepage, Shop, Related Products, Wishlist
 * 
 * Expected $product array with keys:
 * id, name, slug, price, sale_price, primary_image, hover_image, category_name, rating, reviews_count, is_new
 */

if (!isset($product) || empty($product)) {
    return;
}

$price = (float)$product['price'];
$salePrice = !empty($product['sale_price']) && $product['sale_price'] > 0 ? (float)$product['sale_price'] : null;
$effectivePrice = $salePrice ?: $price;
$oldPrice = $salePrice ? $price : null;
$discountPercent = $oldPrice ? calculate_discount_percent($oldPrice, $effectivePrice) : 0;
$productUrl = BASE_URL . '/product.php?slug=' . urlencode($product['slug']);
$wishlistIds = get_wishlist_ids();
$isWishlisted = in_array((int)$product['id'], $wishlistIds);

$primaryImg = !empty($product['primary_image']) ? $product['primary_image'] : 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80';
$hoverImg = !empty($product['hover_image']) ? $product['hover_image'] : $primaryImg;
?>
<div class="product-card" data-product-id="<?= (int)$product['id'] ?>">
    <div class="product-card-media">
        <!-- Badges -->
        <div class="product-badges">
            <?php if (!empty($product['is_new'])): ?>
                <span class="badge badge-new">NEW</span>
            <?php endif; ?>
            <?php if ($discountPercent > 0): ?>
                <span class="badge badge-sale">-<?= $discountPercent ?>%</span>
            <?php endif; ?>
        </div>

        <!-- Wishlist Button -->
        <button type="button" 
                class="btn-wishlist <?= $isWishlisted ? 'active' : '' ?>" 
                data-product-id="<?= (int)$product['id'] ?>" 
                aria-label="Add to Wishlist"
                title="<?= $isWishlisted ? 'In your wishlist' : 'Save to wishlist' ?>">
            <svg class="heart-icon" viewBox="0 0 24 24" fill="<?= $isWishlisted ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </button>

        <!-- Product Image Link with Hover Effect -->
        <a href="<?= $productUrl ?>" class="product-image-link">
            <img src="<?= e($primaryImg) ?>" 
                 alt="<?= e($product['name']) ?>" 
                 class="product-image primary" 
                 loading="lazy">
            <?php if ($hoverImg && $hoverImg !== $primaryImg): ?>
                <img src="<?= e($hoverImg) ?>" 
                     alt="<?= e($product['name']) ?>" 
                     class="product-image hover" 
                     loading="lazy">
            <?php endif; ?>
        </a>

        <!-- Quick View & Add to Bag Actions Overlay -->
        <div class="product-card-actions">
            <button type="button" class="btn-card-action btn-quickview" data-product-id="<?= (int)$product['id'] ?>" data-product-slug="<?= e($product['slug']) ?>">
                <span>Quick View</span>
            </button>
            <button type="button" class="btn-card-action btn-add-bag" data-product-id="<?= (int)$product['id'] ?>">
                <span>Add to Bag</span>
            </button>
        </div>
    </div>

    <!-- Product Details -->
    <div class="product-card-info">
        <?php if (!empty($product['category_name'])): ?>
            <span class="product-category"><?= e($product['category_name']) ?></span>
        <?php endif; ?>
        
        <h3 class="product-title">
            <a href="<?= $productUrl ?>"><?= e($product['name']) ?></a>
        </h3>

        <div class="product-pricing">
            <span class="price-current"><?= format_price($effectivePrice) ?></span>
            <?php if ($oldPrice): ?>
                <span class="price-old"><?= format_price($oldPrice) ?></span>
            <?php endif; ?>
        </div>

        <?php if (!empty($product['rating']) && (float)$product['rating'] > 0): ?>
            <div class="product-meta-rating">
                <?= render_rating_stars((float)$product['rating'], (int)($product['reviews_count'] ?? 0)) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
