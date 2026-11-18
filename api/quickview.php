<?php
/**
 * MAISON ÉLAN - Quick View AJAX API
 * Returns HTML payload for the Quick View modal
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/cart-functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

$product = null;
if ($id > 0) {
    $product = get_product($id);
} elseif (!empty($slug)) {
    $product = get_product($slug);
}

if (!$product) {
    echo '<div class="quickview-error">Silhouette information could not be retrieved.</div>';
    exit;
}

$price = (float)$product['price'];
$salePrice = !empty($product['sale_price']) && $product['sale_price'] > 0 ? (float)$product['sale_price'] : null;
$effectivePrice = $salePrice ?: $price;
$oldPrice = $salePrice ? $price : null;
$discountPercent = $oldPrice ? calculate_discount_percent($oldPrice, $effectivePrice) : 0;
$productUrl = BASE_URL . '/product.php?slug=' . urlencode($product['slug']);

$sizes = array_map('trim', explode(',', $product['sizes'] ?? 'Free Size'));
$colors = array_map('trim', explode(',', $product['colors'] ?? 'Original'));
$img = !empty($product['primary_image']) ? $product['primary_image'] : ($product['images'][0]['image_url'] ?? 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80');
?>
<div class="quickview-modal-grid">
    <div class="quickview-media-col">
        <img src="<?= e($img) ?>" alt="<?= e($product['name']) ?>" class="quickview-main-image">
        <?php if ($discountPercent > 0): ?>
            <span class="badge badge-sale quickview-badge">-<?= $discountPercent ?>%</span>
        <?php endif; ?>
    </div>

    <div class="quickview-info-col">
        <span class="product-category"><?= e($product['category_name'] ?? 'Haute Couture') ?></span>
        <h2 class="quickview-title"><a href="<?= $productUrl ?>"><?= e($product['name']) ?></a></h2>

        <div class="quickview-rating">
            <?= render_rating_stars((float)$product['rating'], (int)($product['reviews_count'] ?? 0)) ?>
        </div>

        <div class="quickview-price-row">
            <span class="price-current"><?= format_price($effectivePrice) ?></span>
            <?php if ($oldPrice): ?>
                <span class="price-old"><?= format_price($oldPrice) ?></span>
            <?php endif; ?>
        </div>

        <p class="quickview-desc"><?= e($product['short_description']) ?></p>

        <form class="quickview-form" id="quickViewForm">
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

            <!-- Color -->
            <?php if (!empty($colors) && count($colors) > 0 && $colors[0] !== ''): ?>
                <div class="variant-group">
                    <label class="variant-label">Color: <strong><?= e($colors[0]) ?></strong></label>
                    <div class="color-swatches-row">
                        <?php foreach ($colors as $idx => $clr): ?>
                            <label class="color-swatch-item">
                                <input type="radio" name="color" value="<?= e($clr) ?>" <?= $idx === 0 ? 'checked' : '' ?>>
                                <span class="color-swatch-chip"><?= e($clr) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Size -->
            <div class="variant-group">
                <label class="variant-label">Size: <strong><?= e($sizes[0]) ?></strong></label>
                <div class="size-buttons-row">
                    <?php foreach ($sizes as $idx => $sz): ?>
                        <label class="size-btn-item">
                            <input type="radio" name="size" value="<?= e($sz) ?>" <?= $idx === 0 ? 'checked' : '' ?>>
                            <span class="size-btn-label"><?= e($sz) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="quickview-actions-row">
                <div class="quantity-stepper">
                    <button type="button" class="qty-btn minus" onclick="var i=this.nextElementSibling;if(parseInt(i.value)>1)i.value=parseInt(i.value)-1;">−</button>
                    <input type="number" name="quantity" class="qty-input" value="1" min="1" max="10">
                    <button type="button" class="qty-btn plus" onclick="var i=this.previousElementSibling;i.value=parseInt(i.value)+1;">+</button>
                </div>

                <button type="submit" class="btn btn-primary btn-add-bag-modal">
                    ADD TO BAG
                </button>
            </div>
        </form>

        <div class="quickview-footer-link">
            <a href="<?= $productUrl ?>">View Full Atelier Silhouette & Fabric Details &rarr;</a>
        </div>
    </div>
</div>
