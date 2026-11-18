<?php
/**
 * MAISON ÉLAN - Customer Registration
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/account.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security session. Please retry.';
    } else {
        $res = customer_register($_POST);
        if ($res['success']) {
            set_flash_message('success', 'Your Maison Élan account has been successfully created. Welcome!');
            redirect(BASE_URL . '/account.php');
        } else {
            $error = $res['message'];
        }
    }
}

$pageTitle = "Create an Account — Maison Élan Haute Couture";
$pageDescription = "Create a Maison Élan boutique account for seamless ordering, personalized recommendations, and exclusive private previews.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page-container site-container">
    <div class="auth-card">
        <div class="auth-card-header">
            <span class="auth-eyebrow">JOIN THE CIRCLE</span>
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Register for bespoke access, express checkout, and private collection launches.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="auth-alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/register.php" method="POST" class="auth-form">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="regFullName" class="form-label">Full Name *</label>
                <input type="text" id="regFullName" name="full_name" class="form-control" placeholder="Aanya Singhania" value="<?= e($_POST['full_name'] ?? '') ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="regEmail" class="form-label">Email Address *</label>
                <input type="email" id="regEmail" name="email" class="form-control" placeholder="name@example.com" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="regPhone" class="form-label">Mobile Number *</label>
                <input type="tel" id="regPhone" name="phone" class="form-control" placeholder="+91 98765 43210" value="<?= e($_POST['phone'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="regPassword" class="form-label">Password (min. 6 characters) *</label>
                <input type="password" id="regPassword" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block btn-lg">CREATE ACCOUNT</button>
            </div>
        </form>

        <div class="auth-card-footer">
            <p>Already a patron? <a href="<?= BASE_URL ?>/login.php" class="auth-link">Sign In Here</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
