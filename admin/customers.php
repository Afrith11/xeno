<?php
/**
 * MAISON ÉLAN - Admin Customers Management
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$adminTitle = "Registered Clients";
$pageHeading = "Atelier Patrons & Customers";

$db = getDB();
$customers = [];
$search = trim($_GET['search'] ?? '');

if ($db) {
    try {
        $sql = "SELECT u.*, COUNT(o.id) as total_orders, COALESCE(SUM(o.grand_total), 0) as total_spent
                FROM users u
                LEFT JOIN orders o ON u.id = o.user_id AND o.order_status NOT IN ('Cancelled')
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $term = '%' . $search . '%';
            $params = [$term, $term, $term];
        }

        $sql .= " GROUP BY u.id ORDER BY u.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll();

    } catch (\Exception $e) {
        set_flash_message('error', 'Error fetching customers: ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-actions-row">
    <div class="search-filter-controls">
        <form action="<?= BASE_URL ?>/admin/customers.php" method="GET" class="admin-filter-form">
            <input type="text" name="search" class="admin-input-search" placeholder="Search patron name, email or phone..." value="<?= e($search) ?>">
            <button type="submit" class="btn btn-outline btn-sm">Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?= BASE_URL ?>/admin/customers.php" class="btn btn-text btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="admin-panel-card">
    <div class="admin-table-responsive">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Patron Name</th>
                    <th>Email Address</th>
                    <th>Mobile</th>
                    <th>City & State</th>
                    <th>Orders Placed</th>
                    <th>Total Spent</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customers)): ?>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><strong><?= e($c['full_name']) ?></strong></td>
                            <td><a href="mailto:<?= e($c['email']) ?>"><?= e($c['email']) ?></a></td>
                            <td><?= e($c['phone'] ?? '—') ?></td>
                            <td><?= e($c['city'] ?? '—') ?><?= !empty($c['state']) ? ', ' . e($c['state']) : '' ?></td>
                            <td><strong><?= (int)$c['total_orders'] ?> orders</strong></td>
                            <td><?= format_price($c['total_spent']) ?></td>
                            <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center py-4">No registered clients found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
