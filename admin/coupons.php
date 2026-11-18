<?php
/**
 * MAISON ÉLAN - Admin Coupons Management
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$adminTitle = "Coupons & Privileges";
$pageHeading = "Atelier Privilege Codes & Promotions";

$db = getDB();
$errors = [];

// Handle Add Coupon
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security session token.';
    }

    $code = strtoupper(trim($_POST['code'] ?? ''));
    $discountType = $_POST['discount_type'] ?? 'percentage';
    $discountVal = (float)($_POST['discount_value'] ?? 0);
    $minOrderVal = (float)($_POST['min_order_value'] ?? 0);
    $maxDiscount = !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null;
    $validFrom = $_POST['valid_from'] ?? date('Y-m-d');
    $validUntil = $_POST['valid_until'] ?? date('Y-m-d', strtotime('+1 year'));
    $usageLimit = (int)($_POST['usage_limit'] ?? 500);

    if (empty($code)) {
        $errors[] = 'Privilege code is required.';
    }
    if ($discountVal <= 0) {
        $errors[] = 'Discount value must be greater than zero.';
    }

    if (empty($errors) && $db) {
        try {
            $stmt = $db->prepare("INSERT INTO coupons 
                (code, discount_type, discount_value, min_order_value, max_discount, valid_from, valid_until, usage_limit, is_active, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");
            $stmt->execute([$code, $discountType, $discountVal, $minOrderVal, $maxDiscount, $validFrom, $validUntil, $usageLimit]);
            set_flash_message('success', "Privilege code '{$code}' created successfully.");
            redirect(BASE_URL . '/admin/coupons.php');
        } catch (\Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle Delete Coupon
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && $db) {
    try {
        $db->prepare("DELETE FROM coupons WHERE id = ?")->execute([(int)$_GET['delete']]);
        set_flash_message('success', 'Privilege code deleted.');
        redirect(BASE_URL . '/admin/coupons.php');
    } catch (\Exception $e) {}
}

$coupons = [];
if ($db) {
    $coupons = $db->query("SELECT * FROM coupons ORDER BY id DESC")->fetchAll();
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if (!empty($errors)): ?>
    <div class="auth-alert error">
        <ul>
            <?php foreach ($errors as $er): ?>
                <li><?= e($er) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-two-cols-layout">
    <!-- Left: Coupons List -->
    <div class="col-wide">
        <div class="admin-panel-card">
            <div class="panel-card-header">
                <h3 class="panel-title">Active Privilege Codes</h3>
            </div>

            <div class="admin-table-responsive">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Benefit</th>
                            <th>Min. Bag</th>
                            <th>Valid Window</th>
                            <th>Redemptions</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($coupons)): ?>
                            <?php foreach ($coupons as $cp): ?>
                                <tr>
                                    <td>
                                        <strong class="code-pill"><?= e($cp['code']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($cp['discount_type'] === 'percentage'): ?>
                                            <strong><?= (float)$cp['discount_value'] ?>% Off</strong>
                                            <?php if (!empty($cp['max_discount'])): ?>
                                                <span class="table-subdate">Max <?= format_price($cp['max_discount']) ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <strong><?= format_price($cp['discount_value']) ?> Off</strong>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= format_price($cp['min_order_value']) ?></td>
                                    <td>
                                        <span class="table-subdate"><?= date('M d, Y', strtotime($cp['valid_from'])) ?> – <?= date('M d, Y', strtotime($cp['valid_until'])) ?></span>
                                    </td>
                                    <td><?= (int)$cp['times_used'] ?> / <?= (int)$cp['usage_limit'] ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/admin/coupons.php?delete=<?= (int)$cp['id'] ?>" class="btn-table-action delete" onclick="return confirm('Delete coupon code?');">🗑</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4">No privilege coupons found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Create New Coupon Form -->
    <div class="col-side">
        <div class="admin-panel-card">
            <div class="panel-card-header">
                <h3 class="panel-title">Issue New Privilege Code</h3>
            </div>

            <form action="<?= BASE_URL ?>/admin/coupons.php" method="POST" class="admin-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="cpCode" class="form-label">Privilege Code *</label>
                    <input type="text" id="cpCode" name="code" class="form-control" placeholder="e.g. FESTIVE20" required style="text-transform:uppercase;">
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="cpType" class="form-label">Type</label>
                        <select id="cpType" name="discount_type" class="form-control">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cpVal" class="form-label">Discount Value *</label>
                        <input type="number" step="0.01" id="cpVal" name="discount_value" class="form-control" placeholder="10 or 500" required>
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="cpMin" class="form-label">Min Bag Value (₹)</label>
                        <input type="number" step="0.01" id="cpMin" name="min_order_value" class="form-control" value="0.00">
                    </div>
                    <div class="form-group">
                        <label for="cpMax" class="form-label">Max Cap (₹, optional)</label>
                        <input type="number" step="0.01" id="cpMax" name="max_discount" class="form-control" placeholder="Optional">
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="cpFrom" class="form-label">Valid From</label>
                        <input type="date" id="cpFrom" name="valid_from" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label for="cpUntil" class="form-label">Valid Until</label>
                        <input type="date" id="cpUntil" name="valid_until" class="form-control" value="<?= date('Y-m-d', strtotime('+1 year')) ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">ACTIVATE PRIVILEGE CODE</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
