<?php
/**
 * MAISON ÉLAN - Order History
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$user = get_logged_in_user();
$orders = [];
$db = getDB();

if ($user && $db) {
    try {
        $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$user['id']]);
        $orders = $stmt->fetchAll();
    } catch (\Exception $e) {}
}

// Guest order lookup by reference number & email
$guestOrder = null;
if (isset($_GET['lookup_order']) && isset($_GET['lookup_email'])) {
    $ref = trim($_GET['lookup_order']);
    $em = trim($_GET['lookup_email']);
    if (!empty($ref) && !empty($em) && $db) {
        try {
            $stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ? AND customer_email = ? LIMIT 1");
            $stmt->execute([$ref, $em]);
            $guestOrder = $stmt->fetch();
        } catch (\Exception $e) {}
    }
}

$pageTitle = "My Orders — Maison Élan Haute Couture";
$pageDescription = "Track current shipments and view historical bespoke orders placed with Maison Élan.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="account-page-container site-container">
    <div class="account-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">Order History</span>
        </nav>
        <h1 class="account-title">YOUR ORDERS & ARCHIVE</h1>
        <p class="account-sub">Track active atelier dispatches and inspect receipt archives.</p>
    </div>

    <div class="account-dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="account-sidebar">
            <div class="account-nav-card">
                <ul class="account-nav-list">
                    <?php if ($user): ?>
                        <li><a href="<?= BASE_URL ?>/account.php" class="account-nav-link">Profile & Address</a></li>
                        <li><a href="<?= BASE_URL ?>/orders.php" class="account-nav-link active">Order History</a></li>
                        <li><a href="<?= BASE_URL ?>/wishlist.php" class="account-nav-link">My Wishlist</a></li>
                        <li><a href="<?= BASE_URL ?>/logout.php" class="account-nav-link text-danger">Sign Out</a></li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>/login.php" class="account-nav-link">Sign In</a></li>
                        <li><a href="<?= BASE_URL ?>/register.php" class="account-nav-link">Register Account</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>

        <!-- Main Orders List -->
        <main class="account-main-content">
            <?php if ($user): ?>
                <?php if (!empty($orders)): ?>
                    <div class="orders-list-wrap">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-summary-card-item">
                                <div class="order-card-top-row">
                                    <div class="order-id-meta">
                                        <span class="order-num-label">Order Reference:</span>
                                        <strong class="order-num"><?= e($order['order_number']) ?></strong>
                                        <span class="order-date"><?= date('F d, Y', strtotime($order['created_at'])) ?></span>
                                    </div>
                                    <div class="order-status-badge-wrap">
                                        <?php
                                        $statusClass = 'status-' . strtolower($order['order_status']);
                                        ?>
                                        <span class="order-badge <?= $statusClass ?>"><?= e($order['order_status']) ?></span>
                                    </div>
                                </div>

                                <div class="order-card-middle-row">
                                    <div class="order-info-chunk">
                                        <span class="chunk-label">Grand Total:</span>
                                        <strong class="chunk-val"><?= format_price($order['grand_total']) ?></strong>
                                    </div>
                                    <div class="order-info-chunk">
                                        <span class="chunk-label">Payment:</span>
                                        <span class="chunk-val"><?= strtoupper(e($order['payment_method'])) ?> (<?= e($order['payment_status']) ?>)</span>
                                    </div>
                                    <div class="order-info-chunk">
                                        <span class="chunk-label">Recipient:</span>
                                        <span class="chunk-val"><?= e($order['customer_name']) ?></span>
                                    </div>
                                </div>

                                <div class="order-card-bottom-row">
                                    <a href="<?= BASE_URL ?>/order-detail.php?order=<?= urlencode($order['order_number']) ?>" class="btn btn-outline btn-sm">Inspect Order Details &rarr;</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="orders-empty-state">
                        <span class="empty-icon">❖</span>
                        <h3>No Orders Yet</h3>
                        <p>You haven't placed any orders with Maison Élan yet. Explore our curated collections to find your signature piece.</p>
                        <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary">Discover Silhouettes</a>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- Guest Order Lookup Form -->
                <div class="guest-order-lookup-card">
                    <h3 class="lookup-title">Track a Boutique Order</h3>
                    <p class="lookup-subtitle">Enter your order reference code and the email address used during checkout.</p>

                    <form action="<?= BASE_URL ?>/orders.php" method="GET" class="lookup-form">
                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="lookupOrder" class="form-label">Order Number *</label>
                                <input type="text" id="lookupOrder" name="lookup_order" class="form-control" placeholder="e.g. ME-2026-1001" value="<?= e($_GET['lookup_order'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="lookupEmail" class="form-label">Email Address *</label>
                                <input type="email" id="lookupEmail" name="lookup_email" class="form-control" placeholder="your-email@example.com" value="<?= e($_GET['lookup_email'] ?? '') ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">TRACK DISPATCH</button>
                    </form>

                    <?php if ($guestOrder): ?>
                        <div class="lookup-result-card" style="margin-top: 30px;">
                            <div class="order-summary-card-item">
                                <div class="order-card-top-row">
                                    <div>
                                        <span class="order-num-label">Order Found:</span>
                                        <strong class="order-num"><?= e($guestOrder['order_number']) ?></strong>
                                    </div>
                                    <span class="order-badge status-<?= strtolower($guestOrder['order_status']) ?>"><?= e($guestOrder['order_status']) ?></span>
                                </div>
                                <div class="order-card-middle-row">
                                    <div>Total: <strong><?= format_price($guestOrder['grand_total']) ?></strong></div>
                                    <div>Status: <?= e($guestOrder['order_status']) ?></div>
                                </div>
                                <a href="<?= BASE_URL ?>/order-detail.php?order=<?= urlencode($guestOrder['order_number']) ?>" class="btn btn-outline btn-sm">View Full Details &rarr;</a>
                            </div>
                        </div>
                    <?php elseif (isset($_GET['lookup_order'])): ?>
                        <p class="text-danger" style="margin-top:20px;">No matching order was found for the details provided. Please check your reference code.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
