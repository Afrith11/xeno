<?php
/**
 * MAISON ÉLAN - Customer Login
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/account.php');
}

$error = '';
$redirectUrl = $_SESSION['redirect_after_login'] ?? (BASE_URL . '/account.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security session. Please retry.';
    } else {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $res = customer_login($email, $password);

        if ($res['success']) {
            unset($_SESSION['redirect_after_login']);
            set_flash_message('success', 'Welcome back to Maison Élan.');
            redirect($redirectUrl);
        } else {
            $error = $res['message'];
        }
    }
}

$pageTitle = "Sign In — Maison Élan Haute Couture";
$pageDescription = "Sign in to your Maison Élan client profile to view order history, track deliveries, and manage your wishlist.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page-container site-container">
    <div class="auth-card">
        <div class="auth-card-header">
            <span class="auth-eyebrow">CLIENT PRIVILEGE</span>
            <h1 class="auth-title">Sign In</h1>
            <p class="auth-subtitle">Welcome back. Enter your credentials to access your atelier account.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="auth-alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login.php" method="POST" class="auth-form">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="loginEmail" class="form-label">Email Address *</label>
                <input type="email" id="loginEmail" name="email" class="form-control" placeholder="name@example.com" value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
            </div>

            <div class="form-group">
                <div class="form-label-row">
                    <label for="loginPassword" class="form-label">Password *</label>
                </div>
                <input type="password" id="loginPassword" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block btn-lg">SIGN IN</button>
            </div>
        </form>

        <div class="auth-card-footer">
            <p>New to Maison Élan? <a href="<?= BASE_URL ?>/register.php" class="auth-link">Create an Account</a></p>
            <div class="demo-credentials-box">
                <span class="demo-tag">DEMO CLIENT CREDENTIALS:</span>
                <p>Email: <code>aanya@example.com</code> | Password: <code>Customer@123</code></p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
