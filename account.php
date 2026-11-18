<?php
/**
 * MAISON ÉLAN - Customer Account Dashboard
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

require_login('account.php');
$user = get_logged_in_user();
$db = getDB();

$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please refresh.';
    } else {
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $pincode = trim($_POST['pincode'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';

        if (empty($fullName)) {
            $error = 'Please enter your full name.';
        } else {
            if ($db) {
                try {
                    if (!empty($newPassword)) {
                        if (strlen($newPassword) < 6) {
                            $error = 'Password must be at least 6 characters.';
                        } else {
                            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                            $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, city = ?, state = ?, pincode = ?, password = ? WHERE id = ?");
                            $stmt->execute([$fullName, $phone, $address, $city, $state, $pincode, $hash, $user['id']]);
                            $success = 'Profile and password updated successfully.';
                        }
                    } else {
                        $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, city = ?, state = ?, pincode = ? WHERE id = ?");
                        $stmt->execute([$fullName, $phone, $address, $city, $state, $pincode, $user['id']]);
                        $success = 'Profile details updated successfully.';
                    }

                    // Refresh session user data
                    $user = get_logged_in_user();
                    $_SESSION['user_name'] = $fullName;

                } catch (\Exception $e) {
                    $error = 'Failed to update profile: ' . $e->getMessage();
                }
            } else {
                $success = 'Profile updated in session.';
            }
        }
    }
}

// Fetch order count
$orderCount = 0;
if ($db && $user) {
    try {
        $cntStmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $cntStmt->execute([$user['id']]);
        $orderCount = (int)$cntStmt->fetchColumn();
    } catch (\Exception $e) {}
}

$pageTitle = "My Account — Maison Élan Haute Couture";
$pageDescription = "Manage your Maison Élan client profile, delivery addresses, and boutique preferences.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="account-page-container site-container">
    <div class="account-header">
        <span class="account-eyebrow">CLIENT SALON</span>
        <h1 class="account-title">Welcome, <?= e($user['full_name'] ?? 'Patron') ?></h1>
        <p class="account-sub">Manage your personal atelier preferences and saved addresses.</p>
    </div>

    <?php if (!empty($success)): ?>
        <div class="auth-alert success"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="auth-alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="account-dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="account-sidebar">
            <div class="account-nav-card">
                <ul class="account-nav-list">
                    <li><a href="<?= BASE_URL ?>/account.php" class="account-nav-link active">Profile & Address</a></li>
                    <li><a href="<?= BASE_URL ?>/orders.php" class="account-nav-link">Order History (<?= $orderCount ?>)</a></li>
                    <li><a href="<?= BASE_URL ?>/wishlist.php" class="account-nav-link">My Wishlist</a></li>
                    <li><a href="<?= BASE_URL ?>/logout.php" class="account-nav-link text-danger">Sign Out</a></li>
                </ul>
            </div>

            <!-- Client Stat summary -->
            <div class="account-stat-card">
                <span class="stat-tag">ATELIER PATRON STATUS</span>
                <h4 class="stat-tier">Maison Privilège</h4>
                <p class="stat-desc">Enjoy complimentary white-glove shipping on all orders and bespoke atelier consultations.</p>
            </div>
        </aside>

        <!-- Main Account Content Form -->
        <main class="account-main-content">
            <div class="account-card-box">
                <h3 class="account-card-title">Personal Profile & Delivery Address</h3>

                <form action="<?= BASE_URL ?>/account.php" method="POST" class="account-form">
                    <?= csrf_field() ?>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="accFullName" class="form-label">Full Name *</label>
                            <input type="text" id="accFullName" name="full_name" class="form-control" value="<?= e($user['full_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="accEmail" class="form-label">Email Address (Read-only)</label>
                            <input type="email" id="accEmail" class="form-control" value="<?= e($user['email'] ?? '') ?>" readonly disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="accPhone" class="form-label">Mobile Number</label>
                        <input type="tel" id="accPhone" name="phone" class="form-control" placeholder="+91 98765 43210" value="<?= e($user['phone'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="accAddress" class="form-label">Default Shipping Address</label>
                        <textarea id="accAddress" name="address" class="form-control" rows="2" placeholder="Street, Building, Apartment"><?= e($user['address'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label for="accCity" class="form-label">City</label>
                            <input type="text" id="accCity" name="city" class="form-control" value="<?= e($user['city'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="accState" class="form-label">State</label>
                            <input type="text" id="accState" name="state" class="form-control" value="<?= e($user['state'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="accPincode" class="form-label">Pincode</label>
                            <input type="text" id="accPincode" name="pincode" class="form-control" value="<?= e($user['pincode'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-divider"></div>

                    <div class="form-group">
                        <label for="accNewPassword" class="form-label">Change Password (leave empty to keep unchanged)</label>
                        <input type="password" id="accNewPassword" name="new_password" class="form-control" placeholder="New password (min 6 characters)">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">SAVE ATELIER CHANGES</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
