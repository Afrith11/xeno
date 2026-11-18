<?php
/**
 * MAISON ÉLAN - Shopping Cart Page
 * Responsive Table, Live Totals, Coupon Handling
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/cart-functions.php';

// Handle synchronous POST fallbacks for quantity / remove / coupon
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add' && !empty($_POST['product_id'])) {
            $pid = (int)$_POST['product_id'];
            $qty = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
            $sz = isset($_POST['size']) ? trim($_POST['size']) : 'Free Size';
            $clr = isset($_POST['color']) ? trim($_POST['color']) : '';
            $res = add_to_cart($pid, $qty, $sz, $clr);
            if ($res['success']) {
                set_flash_message('success', 'Piece added to your shopping bag.');
                if (!empty($_POST['buy_now'])) {
                    redirect(BASE_URL . '/checkout.php');
                }
            } else {
                set_flash_message('error', $res['message']);
            }
            redirect(BASE_URL . '/cart.php');
        }

        if ($action === 'update' && !empty($_POST['item_key']) && isset($_POST['quantity'])) {
            update_cart_quantity($_POST['item_key'], (int)$_POST['quantity']);
            set_flash_message('success', 'Shopping bag updated.');
            redirect(BASE_URL . '/cart.php');
        }

        if ($action === 'remove' && !empty($_POST['item_key'])) {
            remove_from_cart($_POST['item_key']);
            set_flash_message('info', 'Item removed from your shopping bag.');
            redirect(BASE_URL . '/cart.php');
        }

        if ($action === 'apply_coupon' && !empty($_POST['coupon_code'])) {
            $res = apply_coupon_code($_POST['coupon_code']);
            if ($res['success']) {
                set_flash_message('success', $res['message']);
            } else {
                set_flash_message('error', $res['message']);
            }
            redirect(BASE_URL . '/cart.php');
        }

        if ($action === 'remove_coupon') {
            remove_coupon_code();
            set_flash_message('info', 'Privilege code removed.');
            redirect(BASE_URL . '/cart.php');
        }
    }
}

$cart = get_cart_summary();
$pageTitle = "Shopping Bag (" . $cart['item_count'] . ") — Maison Élan";
$pageDescription = "Review the bespoke couture pieces in your shopping bag before proceeding to secure checkout.";

require_once __DIR__ . '/includes/header.php';
?>

<!-- Cart Header -->
<section class="cart-header-banner">
    <div class="site-container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">Shopping Bag</span>
        </nav>
        <h1 class="cart-page-title">YOUR SHOPPING BAG</h1>
        <p class="cart-page-sub">Complimentary boutique packaging and insured priority delivery across India.</p>
    </div>
</section>

<div class="cart-page-container site-container">
    <?php if (empty($cart['items'])): ?>
        <!-- Empty Cart -->
        <div class="cart-empty-view">
            <div class="empty-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="64" height="64">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <h2 class="empty-heading">Your Shopping Bag is Empty</h2>
            <p class="empty-text">Select your next memorable silhouette from our newest arrivals, heritage Banarasi silks, or sculpted evening wear.</p>
            <div class="empty-actions">
                <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary btn-lg">Explore Collections</a>
                <a href="<?= BASE_URL ?>/shop.php?collection=wedding" class="btn btn-outline btn-lg">The Wedding Edit</a>
            </div>
        </div>
    <?php else: ?>
        <!-- Cart Table & Summary -->
        <div class="cart-layout-grid">
            
            <!-- Cart Items Column -->
            <div class="cart-items-column">
                
                <!-- Free Shipping Progress Bar -->
                <div class="free-shipping-card">
                    <?php if ($cart['free_shipping_eligible']): ?>
                        <div class="free-shipping-status achieved">
                            <span class="icon">✨</span>
                            <span>Congratulations! You qualify for <strong>Complimentary Insured White-Glove Delivery</strong>.</span>
                        </div>
                    <?php else: ?>
                        <div class="free-shipping-status pending">
                            <span>Add <strong><?= format_price($cart['amount_to_free_shipping']) ?></strong> more to your bag for <strong>Complimentary Delivery</strong>.</span>
                        </div>
                        <div class="progress-bar-track">
                            <?php $pct = min(100, round(($cart['subtotal'] / FREE_SHIPPING_THRESHOLD) * 100)); ?>
                            <div class="progress-bar-fill" style="width: <?= $pct ?>%;"></div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="cart-items-table-wrap">
                    <table class="cart-items-table">
                        <thead>
                            <tr>
                                <th class="th-product">SILHOUETTE</th>
                                <th class="th-price">PRICE</th>
                                <th class="th-qty">QUANTITY</th>
                                <th class="th-total">TOTAL</th>
                                <th class="th-action"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart['items'] as $item): ?>
                                <tr class="cart-row" data-item-key="<?= e($item['key']) ?>">
                                    <td class="td-product">
                                        <div class="cart-product-cell">
                                            <a href="<?= BASE_URL ?>/product.php?slug=<?= urlencode($item['slug']) ?>" class="cart-item-thumb-link">
                                                <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>" class="cart-item-img">
                                            </a>
                                            <div class="cart-item-details">
                                                <h3 class="cart-item-title">
                                                    <a href="<?= BASE_URL ?>/product.php?slug=<?= urlencode($item['slug']) ?>"><?= e($item['name']) ?></a>
                                                </h3>
                                                <div class="cart-item-variants">
                                                    <span class="variant-tag">Size: <strong><?= e($item['size']) ?></strong></span>
                                                    <?php if (!empty($item['color'])): ?>
                                                        <span class="sep">•</span>
                                                        <span class="variant-tag">Color: <strong><?= e($item['color']) ?></strong></span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="cart-mobile-price">
                                                    <?= format_price($item['price']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="td-price">
                                        <span class="unit-price-val"><?= format_price($item['price']) ?></span>
                                    </td>

                                    <td class="td-qty">
                                        <div class="quantity-stepper cart-qty-stepper">
                                            <button type="button" class="qty-btn minus" data-key="<?= e($item['key']) ?>" data-action="decrease" aria-label="Decrease quantity">−</button>
                                            <input type="number" 
                                                   name="quantity" 
                                                   class="qty-input cart-qty-input" 
                                                   value="<?= (int)$item['quantity'] ?>" 
                                                   min="1" 
                                                   data-key="<?= e($item['key']) ?>" 
                                                   readonly>
                                            <button type="button" class="qty-btn plus" data-key="<?= e($item['key']) ?>" data-action="increase" aria-label="Increase quantity">+</button>
                                        </div>
                                    </td>

                                    <td class="td-total">
                                        <span class="item-line-total" id="lineTotal_<?= e($item['key']) ?>"><?= format_price($item['line_total']) ?></span>
                                    </td>

                                    <td class="td-action">
                                        <button type="button" class="btn-remove-item" data-key="<?= e($item['key']) ?>" aria-label="Remove item" title="Remove piece">
                                            &times;
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-table-footer">
                    <a href="<?= BASE_URL ?>/shop.php" class="btn btn-outline">&larr; Continue Shopping</a>
                </div>
            </div>

            <!-- Order Summary Column -->
            <div class="cart-summary-column">
                <div class="order-summary-card">
                    <h3 class="summary-card-title">BAG SUMMARY</h3>

                    <div class="summary-breakdown">
                        <div class="summary-row">
                            <span class="label">Subtotal</span>
                            <span class="value" id="summarySubtotal"><?= $cart['formatted']['subtotal'] ?></span>
                        </div>

                        <div class="summary-row">
                            <span class="label">Delivery</span>
                            <span class="value" id="summaryShipping"><?= $cart['formatted']['shipping_fee'] ?></span>
                        </div>

                        <?php if ($cart['discount_amount'] > 0): ?>
                            <div class="summary-row discount-row">
                                <span class="label">Privilege Discount (<?= e($cart['coupon']['code'] ?? '') ?>)</span>
                                <span class="value text-success" id="summaryDiscount">-<?= $cart['formatted']['discount_amount'] ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="summary-divider"></div>

                        <div class="summary-row grand-total-row">
                            <span class="label">Grand Total</span>
                            <span class="value" id="summaryGrandTotal"><?= $cart['formatted']['grand_total'] ?></span>
                        </div>
                        <span class="tax-info-small">Inclusive of all Indian taxes and duties.</span>
                    </div>

                    <!-- Coupon Code Accordion / Input -->
                    <div class="coupon-section">
                        <?php if (!empty($cart['coupon'])): ?>
                            <div class="applied-coupon-pill">
                                <div>
                                    <span class="pill-code">Privilege: <?= e($cart['coupon']['code']) ?></span>
                                    <span class="pill-desc">Applied successfully</span>
                                </div>
                                <form action="<?= BASE_URL ?>/cart.php" method="POST" style="margin:0;">
                                    <input type="hidden" name="action" value="remove_coupon">
                                    <button type="submit" class="btn-remove-coupon" title="Remove coupon">&times;</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <form action="<?= BASE_URL ?>/cart.php" method="POST" class="coupon-apply-form" id="cartCouponForm">
                                <input type="hidden" name="action" value="apply_coupon">
                                <label for="couponCodeInput" class="coupon-label">HAVE A PRIVILEGE CODE?</label>
                                <div class="coupon-input-group">
                                    <input type="text" 
                                           name="coupon_code" 
                                           id="couponCodeInput" 
                                           class="coupon-input" 
                                           placeholder="e.g. ELAN10" 
                                           required>
                                    <button type="submit" class="btn btn-outline coupon-btn">Apply</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Checkout CTA Button -->
                    <div class="summary-checkout-actions">
                        <a href="<?= BASE_URL ?>/checkout.php" class="btn btn-primary btn-block btn-lg checkout-btn">
                            PROCEED TO CHECKOUT
                        </a>
                    </div>

                    <!-- Boutique Guarantees -->
                    <div class="boutique-assurances">
                        <div class="assurance-item">
                            <span class="assurance-icon">🔒</span>
                            <span>256-bit Encrypted Secure Checkout</span>
                        </div>
                        <div class="assurance-item">
                            <span class="assurance-icon">📦</span>
                            <span>Gold-Embossed Keepsake Garment Packaging</span>
                        </div>
                        <div class="assurance-item">
                            <span class="assurance-icon">↺</span>
                            <span>Hassle-Free 7-Day Returns & Size Exchanges</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
