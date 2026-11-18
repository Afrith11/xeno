<?php
/**
 * MAISON ÉLAN - Admin View Order & Update Status
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    redirect(BASE_URL . '/admin/orders.php');
}

$db = getDB();
$order = null;
$orderItems = [];

if ($db) {
    // Handle status update POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $newOrderStatus = $_POST['order_status'] ?? 'Pending';
            $newPaymentStatus = $_POST['payment_status'] ?? 'Pending';
            $courierName = trim($_POST['courier_name'] ?? '');
            $trackingNum = trim($_POST['tracking_number'] ?? '');
            $adminNotes = trim($_POST['admin_notes'] ?? '');

            try {
                $upd = $db->prepare("UPDATE orders SET 
                    order_status = ?, payment_status = ?, courier_name = ?, 
                    tracking_number = ?, admin_notes = ?, updated_at = NOW() 
                    WHERE id = ?");
                $upd->execute([$newOrderStatus, $newPaymentStatus, $courierName, $trackingNum, $adminNotes, $orderId]);
                set_flash_message('success', 'Order status & tracking information updated successfully.');
            } catch (\Exception $e) {
                set_flash_message('error', 'Update error: ' . $e->getMessage());
            }
        }
    }

    try {
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if ($order) {
            $itemStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $itemStmt->execute([$orderId]);
            $orderItems = $itemStmt->fetchAll();
        }
    } catch (\Exception $e) {}
}

if (!$order) {
    set_flash_message('error', 'Order not found.');
    redirect(BASE_URL . '/admin/orders.php');
}

$adminTitle = "Order #" . $order['order_number'];
$pageHeading = "Order Review: #" . $order['order_number'];

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-actions-row">
    <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-outline btn-sm">&larr; Return to Orders</a>
    <button type="button" class="btn btn-outline btn-sm" onclick="window.print()">Print Packing Invoice</button>
</div>

<div class="admin-two-cols-layout">
    <!-- Left: Line items and Order Breakdown -->
    <div class="col-wide">
        <div class="admin-panel-card">
            <div class="panel-card-header">
                <h3 class="panel-title">Silhouettes Ordered (<?= count($orderItems) ?> items)</h3>
                <span class="order-date-text">Placed on <?= date('M d, Y \a\t H:i', strtotime($order['created_at'])) ?></span>
            </div>

            <div class="admin-table-responsive">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>Piece</th>
                            <th>Variant</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="admin-prod-cell">
                                        <img src="<?= e($item['product_image']) ?>" alt="" class="admin-prod-thumb">
                                        <strong><?= e($item['product_name']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span>Size: <?= e($item['size']) ?></span>
                                    <?php if (!empty($item['color'])): ?>
                                        <span class="table-subdate">Color: <?= e($item['color']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= format_price($item['unit_price']) ?></td>
                                <td><?= (int)$item['quantity'] ?></td>
                                <td><strong><?= format_price($item['total_price']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-order-totals-box">
                <div class="admin-total-row">
                    <span>Subtotal:</span>
                    <span><?= format_price($order['subtotal']) ?></span>
                </div>
                <div class="admin-total-row">
                    <span>Delivery Courier:</span>
                    <span><?= (float)$order['shipping_fee'] > 0 ? format_price($order['shipping_fee']) : 'Complimentary' ?></span>
                </div>
                <?php if ((float)$order['discount_amount'] > 0): ?>
                    <div class="admin-total-row text-success">
                        <span>Privilege Discount (<?= e($order['coupon_code'] ?? '') ?>):</span>
                        <span>-<?= format_price($order['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="admin-total-row grand">
                    <span>Grand Total:</span>
                    <span><?= format_price($order['grand_total']) ?></span>
                </div>
            </div>
        </div>

        <!-- Client & Shipping Details Card -->
        <div class="admin-panel-card" style="margin-top: 24px;">
            <div class="panel-card-header">
                <h3 class="panel-title">Shipping & Contact Destination</h3>
            </div>
            <div class="admin-address-grid">
                <div>
                    <span class="detail-label">Client Name</span>
                    <p><strong><?= e($order['customer_name']) ?></strong></p>
                </div>
                <div>
                    <span class="detail-label">Mobile Number</span>
                    <p><a href="tel:<?= e($order['customer_phone']) ?>"><?= e($order['customer_phone']) ?></a></p>
                </div>
                <div>
                    <span class="detail-label">Email Address</span>
                    <p><a href="mailto:<?= e($order['customer_email']) ?>"><?= e($order['customer_email']) ?></a></p>
                </div>
                <div style="grid-column: span 3;">
                    <span class="detail-label">Street Address</span>
                    <p><?= nl2br(e($order['shipping_address'])) ?></p>
                    <p><strong><?= e($order['city']) ?>, <?= e($order['state']) ?> – <?= e($order['pincode']) ?></strong></p>
                </div>
                <?php if (!empty($order['notes'])): ?>
                    <div style="grid-column: span 3;">
                        <span class="detail-label">Client Delivery Notes</span>
                        <p class="admin-notes-callout">“<?= e($order['notes']) ?>”</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right: Fulfillment & Payment Status Update Form -->
    <div class="col-side">
        <div class="admin-panel-card">
            <div class="panel-card-header">
                <h3 class="panel-title">Fulfillment & Status</h3>
            </div>

            <form action="<?= BASE_URL ?>/admin/order-view.php?id=<?= $orderId ?>" method="POST" class="admin-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="orderStatus" class="form-label">Order Fulfillment Status</label>
                    <select id="orderStatus" name="order_status" class="form-control">
                        <?php foreach (['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered', 'Cancelled'] as $st): ?>
                            <option value="<?= $st ?>" <?= $order['order_status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="paymentStatus" class="form-label">Payment Status</label>
                    <select id="paymentStatus" name="payment_status" class="form-control">
                        <?php foreach (['Pending', 'Paid', 'Failed', 'COD', 'Refunded'] as $pm): ?>
                            <option value="<?= $pm ?>" <?= $order['payment_status'] === $pm ? 'selected' : '' ?>><?= $pm ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="courierName" class="form-label">Courier Service</label>
                    <input type="text" id="courierName" name="courier_name" class="form-control" placeholder="e.g. BlueDart / Delhivery / DHL" value="<?= e($order['courier_name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="trackingNum" class="form-label">AWB Tracking Number</label>
                    <input type="text" id="trackingNum" name="tracking_number" class="form-control" placeholder="e.g. BLU19827364" value="<?= e($order['tracking_number'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="adminNotes" class="form-label">Internal Atelier Notes</label>
                    <textarea id="adminNotes" name="admin_notes" class="form-control" rows="3" placeholder="Private internal staff notes..."><?= e($order['admin_notes'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">SAVE STATUS UPDATES</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
