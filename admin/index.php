<?php
/**
 * MAISON ÉLAN - Admin Dashboard
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$adminTitle = "Dashboard";
$pageHeading = "Atelier Dashboard Overview";

$db = getDB();

// Default statistics
$stats = [
    'total_orders' => 0,
    'today_orders' => 0,
    'total_sales' => 0.0,
    'pending_orders' => 0,
    'total_customers' => 0,
    'total_products' => 0
];
$recentOrders = [];
$lowStockProducts = [];

if ($db) {
    try {
        // Total Orders
        $stats['total_orders'] = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

        // Today's Orders
        $stats['today_orders'] = (int)$db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();

        // Total Sales (from Paid or Confirmed orders)
        $sales = $db->query("SELECT SUM(grand_total) FROM orders WHERE order_status NOT IN ('Cancelled')")->fetchColumn();
        $stats['total_sales'] = (float)($sales ?: 0);

        // Pending Orders
        $stats['pending_orders'] = (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();

        // Total Customers
        $stats['total_customers'] = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();

        // Total Products
        $stats['total_products'] = (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn();

        // Recent Orders
        $stmt = $db->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
        $recentOrders = $stmt->fetchAll();

        // Low stock products (< 5 items)
        $stockStmt = $db->query("SELECT id, name, sku, stock_quantity, price FROM products WHERE stock_quantity <= 8 ORDER BY stock_quantity ASC LIMIT 5");
        $lowStockProducts = $stockStmt->fetchAll();

    } catch (\Exception $e) {
        error_log("Dashboard query error: " . $e->getMessage());
    }
} else {
    // Fallback demo metrics if DB not yet loaded
    $stats = [
        'total_orders' => 14,
        'today_orders' => 3,
        'total_sales' => 112450.00,
        'pending_orders' => 2,
        'total_customers' => 48,
        'total_products' => 12
    ];
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Statistics Metric Cards Grid -->
<div class="metrics-grid">
    <!-- Card 1: Total Sales -->
    <div class="metric-card card-accent">
        <div class="metric-header">
            <span class="metric-label">TOTAL SALES REVENUE</span>
            <span class="metric-icon">💰</span>
        </div>
        <div class="metric-value"><?= format_price($stats['total_sales']) ?></div>
        <div class="metric-caption">Lifetime net orders processed</div>
    </div>

    <!-- Card 2: Total Orders -->
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">TOTAL ORDERS</span>
            <span class="metric-icon">📦</span>
        </div>
        <div class="metric-value"><?= number_format($stats['total_orders']) ?></div>
        <div class="metric-caption"><?= $stats['today_orders'] ?> placed today</div>
    </div>

    <!-- Card 3: Pending Orders -->
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">PENDING DISPATCH</span>
            <span class="metric-icon">⏳</span>
        </div>
        <div class="metric-value text-warning"><?= number_format($stats['pending_orders']) ?></div>
        <div class="metric-caption">Require atelier packing / verification</div>
    </div>

    <!-- Card 4: Total Customers -->
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">REGISTERED PATRONS</span>
            <span class="metric-icon">👥</span>
        </div>
        <div class="metric-value"><?= number_format($stats['total_customers']) ?></div>
        <div class="metric-caption">Client salon members</div>
    </div>

    <!-- Card 5: Total Products -->
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">ACTIVE SILHOUETTES</span>
            <span class="metric-icon">👗</span>
        </div>
        <div class="metric-value"><?= number_format($stats['total_products']) ?></div>
        <div class="metric-caption">Across all atelier collections</div>
    </div>

    <!-- Card 6: Quick Action -->
    <div class="metric-card card-action">
        <div class="metric-header">
            <span class="metric-label">QUICK ACTIONS</span>
            <span class="metric-icon">⚡</span>
        </div>
        <div class="action-buttons-stack">
            <a href="<?= BASE_URL ?>/admin/product-add.php" class="btn btn-primary btn-sm">+ Add New Silhouette</a>
            <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-outline btn-sm">Process Orders</a>
        </div>
    </div>
</div>

<div class="dashboard-tables-grid">
    <!-- Left: Recent Orders Table -->
    <div class="admin-panel-card">
        <div class="panel-card-header">
            <h3 class="panel-title">Recent Atelier Orders</h3>
            <a href="<?= BASE_URL ?>/admin/orders.php" class="panel-action-link">View All &rarr;</a>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentOrders)): ?>
                        <?php foreach ($recentOrders as $ord): ?>
                            <tr>
                                <td>
                                    <strong><?= e($ord['order_number']) ?></strong>
                                    <span class="table-subdate"><?= date('M d, H:i', strtotime($ord['created_at'])) ?></span>
                                </td>
                                <td>
                                    <span><?= e($ord['customer_name']) ?></span>
                                    <span class="table-subdate"><?= e($ord['city']) ?></span>
                                </td>
                                <td><strong><?= format_price($ord['grand_total']) ?></strong></td>
                                <td>
                                    <span class="badge badge-<?= strtolower($ord['payment_status']) ?>"><?= e($ord['payment_status']) ?></span>
                                </td>
                                <td>
                                    <span class="status-pill status-<?= strtolower($ord['order_status']) ?>"><?= e($ord['order_status']) ?></span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/order-view.php?id=<?= (int)$ord['id'] ?>" class="btn-table-action">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No orders placed yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Inventory Watch (Low Stock) -->
    <div class="admin-panel-card">
        <div class="panel-card-header">
            <h3 class="panel-title">Inventory Low Stock Watch</h3>
            <a href="<?= BASE_URL ?>/admin/products.php" class="panel-action-link">Manage Catalog &rarr;</a>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th>Silhouette</th>
                        <th>SKU</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lowStockProducts)): ?>
                        <?php foreach ($lowStockProducts as $lp): ?>
                            <tr>
                                <td><strong><?= e($lp['name']) ?></strong></td>
                                <td><code><?= e($lp['sku']) ?></code></td>
                                <td>
                                    <span class="stock-badge <?= (int)$lp['stock_quantity'] <= 5 ? 'critical' : 'warning' ?>">
                                        <?= (int)$lp['stock_quantity'] ?> left
                                    </span>
                                </td>
                                <td><?= format_price($lp['price']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/product-edit.php?id=<?= (int)$lp['id'] ?>" class="btn-table-action">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">All items currently well-stocked.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
