<?php
/**
 * MAISON ÉLAN - Order Detail View
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$orderNumber = isset($_GET['order']) ? trim($_GET['order']) : '';
if (empty($orderNumber)) {
    redirect(BASE_URL . '/orders.php');
}

$db = getDB();
$order = null;
$items = [];

if ($db) {
    try {
        $stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
        $stmt->execute([$orderNumber]);
        $order = $stmt->fetch();

        if ($order) {
            $itemStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $itemStmt->execute([$order['id']]);
            $items = $itemStmt->fetchAll();
        }
    } catch (\Exception $e) {}
}

if (!$order) {
    set_flash_message('error', 'Order not found.');
    redirect(BASE_URL . '/orders.php');
}

$pageTitle = "Order Details #" . e($order['order_number']) . " — Maison Élan";
$pageDescription = "View complete receipt and shipment status for order #" . e($order['order_number']);

require_once __DIR__ . '/includes/header.php';
?>

<div class="account-page-container site-container">
    <div class="account-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <a href="<?= BASE_URL ?>/orders.php">Order History</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current"><?= e($order['order_number']) ?></span>
        </nav>
        <div class="order-detail-headline-row">
            <div>
                <h1 class="account-title">ORDER #<?= e($order['order_number']) ?></h1>
                <p class="account-sub">Placed on <?= date('F d, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
            </div>
            <div class="order-detail-actions">
                <button type="button" class="btn btn-outline" onclick="window.print()">Print Invoice</button>
            </div>
        </div>
    </div>

    <!-- Order Status Flow Tracker -->
    <div class="order-tracker-card">
        <h3 class="tracker-title">Fulfillment Progress</h3>
        <?php
        $statuses = ['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered'];
        $currentIdx = array_search($order['order_status'], $statuses);
        if ($currentIdx === false && $order['order_status'] === 'Cancelled') {
            $currentIdx = -1;
        }
        ?>
        <?php if ($order['order_status'] === 'Cancelled'): ?>
            <div class="alert alert-danger">This order has been cancelled.</div>
        <?php else: ?>
            <div class="tracker-stepper">
                <?php foreach ($statuses as $idx => $st): ?>
                    <?php 
                    $isDone = ($currentIdx !== false && $idx <= $currentIdx);
                    $isCurrent = ($currentIdx !== false && $idx === $currentIdx);
                    ?>
                    <div class="step-point <?= $isDone ? 'completed' : '' ?> <?= $isCurrent ? 'current' : '' ?>">
                        <div class="step-circle"><?= $isDone ? '✓' : ($idx + 1) ?></div>
                        <span class="step-name"><?= $st ?></span>
                    </div>
                    <?php if ($idx < count($statuses) - 1): ?>
                        <div class="step-line <?= ($currentIdx !== false && $idx < $currentIdx) ? 'completed' : '' ?>"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($order['tracking_number'])): ?>
            <div class="courier-tracking-row">
                <span>Courier: <strong><?= e($order['courier_name'] ?? 'Express Courier') ?></strong></span>
                <span class="sep">•</span>
                <span>AWB Tracking: <strong><?= e($order['tracking_number']) ?></strong></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Items and Summary Columns -->
    <div class="order-detail-grid">
        <div class="order-detail-items-col">
            <div class="account-card-box">
                <h3 class="account-card-title">Ensembles & Garments (<?= count($items) ?>)</h3>
                
                <div class="receipt-items-list">
                    <?php foreach ($items as $item): ?>
                        <div class="receipt-item-row">
                            <div class="receipt-item-thumb">
                                <img src="<?= e($item['product_image']) ?>" alt="<?= e($item['product_name']) ?>">
                            </div>
                            <div class="receipt-item-details">
                                <h4 class="receipt-item-name"><?= e($item['product_name']) ?></h4>
                                <span class="receipt-item-variant">Size: <strong><?= e($item['size']) ?></strong><?= !empty($item['color']) ? ' | Color: <strong>' . e($item['color']) . '</strong>' : '' ?></span>
                                <span class="receipt-item-qty">Quantity: <?= (int)$item['quantity'] ?> × <?= format_price($item['unit_price']) ?></span>
                            </div>
                            <div class="receipt-item-price">
                                <?= format_price($item['total_price']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="receipt-calc-table">
                    <div class="calc-row">
                        <span>Subtotal:</span>
                        <span><?= format_price($order['subtotal']) ?></span>
                    </div>
                    <div class="calc-row">
                        <span>Insured Delivery:</span>
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
            </div>
        </div>

        <div class="order-detail-meta-col">
            <!-- Shipping Card -->
            <div class="account-card-box">
                <h3 class="account-card-title">Delivery Destination</h3>
                <div class="meta-address-block">
                    <p class="recipient-name"><strong><?= e($order['customer_name']) ?></strong></p>
                    <p><?= nl2br(e($order['shipping_address'])) ?></p>
                    <p><?= e($order['city']) ?>, <?= e($order['state']) ?> – <?= e($order['pincode']) ?></p>
                    <p class="meta-contact">Mobile: <?= e($order['customer_phone']) ?></p>
                    <p class="meta-contact">Email: <?= e($order['customer_email']) ?></p>
                </div>
            </div>

            <!-- Payment Status Card -->
            <div class="account-card-box">
                <h3 class="account-card-title">Payment Information</h3>
                <div class="meta-payment-block">
                    <div class="meta-row">
                        <span>Method:</span>
                        <strong><?= strtoupper(e($order['payment_method'])) ?></strong>
                    </div>
                    <div class="meta-row">
                        <span>Payment Status:</span>
                        <span class="order-badge status-<?= strtolower($order['payment_status']) ?>"><?= e($order['payment_status']) ?></span>
                    </div>
                    <?php if (!empty($order['notes'])): ?>
                        <div class="delivery-notes">
                            <span>Instructions:</span>
                            <p>“<?= e($order['notes']) ?>”</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
