<?php
/**
 * MAISON ÉLAN - 1-Click Database Setup & Diagnostics Tool
 * Simplifies Hostinger Shared Hosting Deployment
 */

require_once __DIR__ . '/config/config.php';

$step = $_GET['step'] ?? 'check';
$message = '';
$error = '';

$phpVersionOk = version_compare(PHP_VERSION, '8.0.0', '>=');
$pdoAvailable = extension_loaded('pdo') && extension_loaded('pdo_mysql');
$uploadsWritable = is_writable(__DIR__ . '/assets/uploads/products') || is_writable(__DIR__ . '/assets/uploads');

// Execute Database Installation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_install'])) {
    $dbHost = trim($_POST['db_host'] ?? DB_HOST);
    $dbName = trim($_POST['db_name'] ?? DB_NAME);
    $dbUser = trim($_POST['db_user'] ?? DB_USER);
    $dbPass = trim($_POST['db_pass'] ?? DB_PASS);

    try {
        // Connect to MySQL server
        $dsn = "mysql:host={$dbHost};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Create Database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbName}`");

        // Read database.sql
        $sqlPath = __DIR__ . '/database.sql';
        if (!file_exists($sqlPath)) {
            throw new Exception("database.sql file not found in root directory.");
        }

        $sql = file_get_contents($sqlPath);
        // Execute queries
        $pdo->exec($sql);

        // Update config/config.php if values were customized
        if ($dbHost !== DB_HOST || $dbName !== DB_NAME || $dbUser !== DB_USER || $dbPass !== DB_PASS) {
            $configFile = __DIR__ . '/config/config.php';
            $configContent = file_get_contents($configFile);
            $configContent = preg_replace("/define\('DB_HOST',.*?\);/", "define('DB_HOST', getenv('DB_HOST') ?: '{$dbHost}');", $configContent);
            $configContent = preg_replace("/define\('DB_NAME',.*?\);/", "define('DB_NAME', getenv('DB_NAME') ?: '{$dbName}');", $configContent);
            $configContent = preg_replace("/define\('DB_USER',.*?\);/", "define('DB_USER', getenv('DB_USER') ?: '{$dbUser}');", $configContent);
            $configContent = preg_replace("/define\('DB_PASS',.*?\);/", "define('DB_PASS', getenv('DB_PASS') ?: '{$dbPass}');", $configContent);
            file_put_contents($configFile, $configContent);
        }

        $message = "Installation completed successfully! All 13 boutique tables and realistic designer collections have been loaded.";
        $step = 'complete';

    } catch (Exception $e) {
        $error = "Database Installation Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atelier Installation & Diagnostics — MAISON ÉLAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --charcoal: #161616;
            --ivory: #FAF7F2;
            --gold: #C5A880;
            --gold-dark: #A68B63;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--ivory);
            color: var(--charcoal);
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .installer-card {
            background: #FFFFFF;
            max-width: 680px;
            width: 100%;
            padding: 40px;
            border-radius: 4px;
            border: 1px solid rgba(22, 22, 22, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            letter-spacing: 0.14em;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 0.72rem;
            letter-spacing: 0.22em;
            color: var(--gold-dark);
            text-transform: uppercase;
        }
        .check-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(22, 22, 22, 0.08);
            font-size: 0.88rem;
        }
        .status-ok { color: #10B981; font-weight: 600; }
        .status-fail { color: #EF4444; font-weight: 600; }
        .alert {
            padding: 14px;
            border-radius: 4px;
            font-size: 0.86rem;
            margin: 20px 0;
        }
        .alert-success { background: #D1FAE5; color: #065F46; }
        .alert-error { background: #FEE2E2; color: #991B1B; }
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #CBD5E1;
            border-radius: 4px;
            font-size: 0.9rem;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            width: 100%;
            padding: 14px;
            background: var(--charcoal);
            color: #FFFFFF;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-top: 10px;
        }
        .btn:hover {
            background: var(--gold-dark);
        }
        .credentials-box {
            background: #F8FAFC;
            padding: 16px;
            border-radius: 4px;
            border-left: 3px solid var(--gold);
            margin: 20px 0;
            font-size: 0.85rem;
        }
        .credentials-box code {
            color: var(--gold-dark);
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="installer-card">
        <div class="brand-header">
            <h1 class="brand-title">MAISON ÉLAN</h1>
            <span class="brand-subtitle">Atelier Setup & Diagnostics</span>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($step === 'complete'): ?>
            <div class="credentials-box">
                <h4>Default Access Credentials:</h4>
                <p><strong>Admin Portal:</strong> <code><?= BASE_URL ?>/admin/</code></p>
                <p>Username: <code>admin</code> | Password: <code>Admin@12345</code></p>
                <hr style="border:0; border-top:1px solid #E2E8F0; margin:10px 0;">
                <p><strong>Test Customer:</strong></p>
                <p>Email: <code>aanya@example.com</code> | Password: <code>Customer@123</code></p>
            </div>
            <a href="<?= BASE_URL ?>/" class="btn">Launch Boutique Front &rarr;</a>
        <?php else: ?>
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 1rem; margin-bottom: 12px; border-bottom: 1px solid #E2E8F0; padding-bottom: 6px;">Hosting Diagnostics</h3>
                
                <div class="check-item">
                    <span>PHP Version (8.0+ required):</span>
                    <span class="<?= $phpVersionOk ? 'status-ok' : 'status-fail' ?>"><?= PHP_VERSION ?> (<?= $phpVersionOk ? 'Passed' : 'Update Required' ?>)</span>
                </div>
                <div class="check-item">
                    <span>PDO & MySQL Drivers:</span>
                    <span class="<?= $pdoAvailable ? 'status-ok' : 'status-fail' ?>"><?= $pdoAvailable ? 'Active' : 'Missing' ?></span>
                </div>
                <div class="check-item">
                    <span>Uploads Directory Writeable:</span>
                    <span class="<?= $uploadsWritable ? 'status-ok' : 'status-fail' ?>"><?= $uploadsWritable ? 'Yes' : 'Check permissions' ?></span>
                </div>
            </div>

            <form action="install.php" method="POST">
                <h3 style="font-size: 1rem; margin-bottom: 12px; border-bottom: 1px solid #E2E8F0; padding-bottom: 6px;">Database Setup</h3>
                <p style="font-size: 0.82rem; color: #64748B; margin-bottom: 16px;">
                    Enter your Hostinger MySQL credentials below. The installer will automatically create the database if needed and import the 13 tables and designer collection seed data.
                </p>

                <div class="form-group">
                    <label class="form-label">Database Host</label>
                    <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars(DB_HOST) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Database Name</label>
                    <input type="text" name="db_name" class="form-control" value="<?= htmlspecialchars(DB_NAME) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Database Username</label>
                    <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars(DB_USER) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Database Password</label>
                    <input type="password" name="db_pass" class="form-control" value="<?= htmlspecialchars(DB_PASS) ?>" placeholder="Your MySQL user password">
                </div>

                <button type="submit" name="run_install" value="1" class="btn">Execute 1-Click Installation</button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
