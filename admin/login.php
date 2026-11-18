<?php
/**
 * MAISON ÉLAN - Admin Login
 */

require_once __DIR__ . '/includes/admin-auth.php';

if (is_admin_logged_in()) {
    redirect(BASE_URL . '/admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security session. Please retry.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $res = admin_login($username, $password);

        if ($res['success']) {
            redirect(BASE_URL . '/admin/index.php');
        } else {
            $error = $res['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atelier Administration Login — MAISON ÉLAN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/admin.css?v=1.0">
</head>
<body class="admin-login-body">

    <div class="admin-login-card">
        <div class="login-brand-header">
            <h1 class="login-logo">MAISON ÉLAN</h1>
            <span class="login-tagline">ATELIER ADMINISTRATION</span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="auth-alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/admin/login.php" method="POST" class="admin-login-form">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="adminUsername" class="form-label">Username or Email *</label>
                <input type="text" id="adminUsername" name="username" class="form-control" placeholder="admin" value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="adminPassword" class="form-label">Password *</label>
                <input type="password" id="adminPassword" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">ENTER ATELIER CONSOLE</button>
        </form>

        <div class="login-credentials-hint">
            <strong>DEFAULT ADMIN CREDENTIALS:</strong>
            <p>Username: <code>admin</code></p>
            <p>Password: <code>Admin@12345</code></p>
        </div>

        <div class="login-back-home">
            <a href="<?= BASE_URL ?>/">&larr; Return to Boutique Front</a>
        </div>
    </div>

</body>
</html>
