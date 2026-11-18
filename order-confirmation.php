<?php
/**
 * MAISON ÉLAN - Order Confirmation
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$orderNumber = isset($_GET['order']) ? trim($_GET['order']) : '';

$order = null;
$orderItems = [];

if (!empty($orderNumber)) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
            $stmt->execute([$orderNumber]);
            $order = $stmt->fetch();

            if ($order) {
                $itemStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                $itemStmt->execute([$order['id']]);
                $orderItems = $itemStmt->fetchAll();
            }
        } catch (\Exception $e) {}
    }
}

// Fallback session info if DB was bypassed or freshly placed
if (!$order && !empty($_SESSION['last_order'])) {
    $order = [
        'order_number' => $_SESSION['last_order']['order_number'],
        'customer_name' => $_SESSION['last_order']['customer_name'],
        'customer_email' => $_SESSION['last_order']['email'],
        'customer_phone' => '+91 98765 43210',
        'shipping_address' => 'Customer Shipping Destination',
        'city' => 'Delhi',
        'state' => 'Delhi',
        'pincode' => '110001',
        'subtotal' => $_SESSION['last_order']['grand_total'],
        'shipping_fee' => 0.00,
        'discount_amount' => 0.00,
        'grand_total' => $_SESSION['last_order']['grand_total'],
        'payment_method' => $_SESSION['last_order']['payment_method'],
        'payment_status' => ($_SESSION['last_order']['payment_method'] === 'cod') ? 'COD' : 'Paid',
        'order_status' => 'Confirmed',
        'created_at' => date('Y-m-d H:i:s')
    ];
}

if (!$order) {
    redirect(BASE_URL . '/shop.php');
}

$pageTitle = "Order Confirmed — " . e($order['order_number']) . " — Maison Élan";
$pageDescription = "Thank you for your order at Maison Élan. Your designer pieces are being prepared by our atelier.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="confirmation-container site-container">
    <div class="confirmation-card">
        
        <div class="confirmation-emblem-wrap">
            <div class="confirmation-icon">✓</div>
            <span class="confirmation-tag">HAUTE COUTURE CONFIRMATION</span>
            <h1 class="confirmation-title">Thank You, <?= e($order['customer_name']) ?></h1>
            <p class="confirmation-subtitle">Your bespoke order has been registered with our New Delhi atelier.</p>
        </div>

        <div class="order-ref-banner">
            <div class="ref-block">
                <span class="ref-label">Order Reference Number</span>
                <span class="ref-code"><?= e($order['order_number']) ?></span>
            </div>
            <div class="ref-block">
                <span class="ref-label">Estimated Delivery</span>
                <span class="ref-val">3 – 5 Business Days</span>
            </div>
            <div class="ref-block">
                <span class="ref-label">Payment Mode</span>
                <span class="ref-val"><?= strtoupper(e($order['payment_method'])) ?> (<?= e($order['payment_status']) ?>)</span>
            </div>
        </div>

        <!-- Order Summary Breakdown -->
        <div class="order-receipt-box">
            <h3 class="receipt-box-title">Order Summary</h3>

            <?php if (!empty($orderItems)): ?>
                <div class="receipt-items-list">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="receipt-item-row">
                            <div class="receipt-item-thumb">
                                <img src="<?= e($item['product_image']) ?>" alt="<?= e($item['product_name']) ?>">
                            </div>
                            <div class="receipt-item-details">
                                <h4 class="receipt-item-name"><?= e($item['product_name']) ?></h4>
                                <span class="receipt-item-variant">Size: <?= e($item['size']) ?><?= !empty($item['color']) ? ' | Color: ' . e($item['color']) : '' ?> • Qty: <?= (int)$item['quantity'] ?></span>
                            </div>
                            <div class="receipt-item-price">
                                <?= format_price($item['total_price']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="receipt-calc-table">
                <div class="calc-row">
                    <span>Subtotal:</span>
                    <span><?= format_price($order['subtotal']) ?></span>
                </div>
                <div class="calc-row">
                    <span>Insured Courier:</span>
                    <span><?= (float)$order['shipping_fee'] > 0 ? format_price($order['shipping_fee']) : 'Complimentary' ?></span>
                </div>
                <?php if ((float)$order['discount_amount'] > 0): ?>
                    <div class="calc-row text-success">
                        <span>Privilege Discount (<?= e($order['coupon_code'] ?? '') ?>):</span>
                        <span>-<?= format_price($order['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="calc-row total-highlight">
                    <span>Grand Total:</span>
                    <span class="grand-val"><?= format_price($order['grand_total']) ?></span>
                </div>
            </div>

            <!-- Shipping address on confirmation -->
            <div class="receipt-shipping-address">
                <h4 class="address-title">Delivering To:</h4>
                <p><?= nl2br(e($order['shipping_address'])) ?></p>
                <p><?= e($order['city']) ?>, <?= e($order['state']) ?> – <?= e($order['pincode']) ?></p>
                <p>Contact: <?= e($order['customer_phone']) ?> | Email: <?= e($order['customer_email']) ?></p>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="confirmation-next-steps">
            <h4 class="steps-heading">What Happens Next?</h4>
            <div class="steps-grid">
                <div class="step-card">
                    <span class="step-counter">1</span>
                    <h5>Atelier Inspection</h5>
                    <p>Our master tailors inspect stitching, lining, and embroidery perfection.</p>
                </div>
                <div class="step-card">
                    <span class="step-counter">2</span>
                    <h5>Gold-Embossed Packaging</h5>
                    <p>Sealed in signature archival dust covers and keepsake boxes.</p>
                </div>
                <div class="step-card">
                    <span class="step-counter">3</span>
                    <h5>White-Glove Dispatch</h5>
                    <p>Live SMS tracking details dispatched to <?= e($order['customer_phone']) ?>.</p>
                </div>
            </div>
        </div>

        <div class="confirmation-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()">Print Atelier Receipt</button>
            <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary">Continue Browsing</a>
            <a href="<?= BASE_URL ?>/orders.php" class="btn btn-text">View All Orders</a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
