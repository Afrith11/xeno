<?php
/**
 * MAISON ÉLAN - Admin Orders Management
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$adminTitle = "Orders Management";
$pageHeading = "Atelier Client Orders";

$db = getDB();
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$paymentFilter = trim($_GET['payment'] ?? '');

$orders = [];

if ($db) {
    try {
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (order_number LIKE ? OR customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ?)";
            $term = '%' . $search . '%';
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        if (!empty($statusFilter)) {
            $sql .= " AND order_status = ?";
            $params[] = $statusFilter;
        }

        if (!empty($paymentFilter)) {
            $sql .= " AND payment_status = ?";
            $params[] = $paymentFilter;
        }

        $sql .= " ORDER BY id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();

    } catch (\Exception $e) {
        set_flash_message('error', 'Error fetching orders: ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-actions-row">
    <div class="search-filter-controls">
        <form action="<?= BASE_URL ?>/admin/orders.php" method="GET" class="admin-filter-form">
            <input type="text" name="search" class="admin-input-search" placeholder="Search order #, client, email..." value="<?= e($search) ?>">

            <select name="status" class="admin-select" onchange="this.form.submit()">
                <option value="">All Order Statuses</option>
                <?php foreach (['Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered', 'Cancelled'] as $st): ?>
                    <option value="<?= $st ?>" <?= $statusFilter === $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>

            <select name="payment" class="admin-select" onchange="this.form.submit()">
                <option value="">All Payment Modes</option>
                <?php foreach (['COD', 'Paid', 'Pending', 'Failed', 'Refunded'] as $pm): ?>
                    <option value="<?= $pm ?>" <?= $paymentFilter === $pm ? 'selected' : '' ?>><?= $pm ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
            <?php if (!empty($search) || !empty($statusFilter) || !empty($paymentFilter)): ?>
                <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-text btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="admin-panel-card">
    <div class="admin-table-responsive">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Order Reference</th>
                    <th>Date</th>
                    <th>Client Name & Location</th>
                    <th>Grand Total</th>
                    <th>Payment Mode</th>
                    <th>Fulfillment Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td>
                                <strong><?= e($ord['order_number']) ?></strong>
                            </td>
                            <td><?= date('M d, Y', strtotime($ord['created_at'])) ?></td>
                            <td>
                                <div><strong><?= e($ord['customer_name']) ?></strong></div>
                                <span class="table-subdate"><?= e($ord['city']) ?>, <?= e($ord['state']) ?></span>
                            </td>
                            <td><strong><?= format_price($ord['grand_total']) ?></strong></td>
                            <td>
                                <span class="badge badge-<?= strtolower($ord['payment_status']) ?>">
                                    <?= strtoupper(e($ord['payment_method'])) ?>: <?= e($ord['payment_status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-pill status-<?= strtolower($ord['order_status']) ?>">
                                    <?= e($ord['order_status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/order-view.php?id=<?= (int)$ord['id'] ?>" class="btn-table-action">Inspect &rarr;</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">No matching orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
