<?php
/**
 * MAISON ÉLAN - Admin Authentication & Authorization
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

function get_admin_user(): ?array {
    if (!is_admin_logged_in()) {
        return null;
    }
    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'] ?? 'admin',
        'full_name' => $_SESSION['admin_name'] ?? 'Atelier Admin',
        'role' => $_SESSION['admin_role'] ?? 'superadmin'
    ];
}

function require_admin(): void {
    if (!is_admin_logged_in()) {
        header("Location: " . BASE_URL . "/admin/login.php");
        exit;
    }
}

function admin_login(string $username, string $password): array {
    $username = trim($username);
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Please provide username and password.'];
    }

    $db = getDB();
    if (!$db) {
        // Fallback demo admin authentication if DB is not connected
        if ($username === 'admin' && $password === 'Admin@12345') {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_username'] = 'admin';
            $_SESSION['admin_name'] = 'Maison Élan Atelier';
            $_SESSION['admin_role'] = 'superadmin';
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Database connection failed.'];
    }

    try {
        $stmt = $db->prepare("SELECT * FROM admin_users WHERE (username = ? OR email = ?) AND is_active = 1 LIMIT 1");
        $stmt->execute([$username, $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];

            // Update last login
            $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);

            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Invalid admin username or password.'];
        }
    } catch (\Exception $e) {
        error_log("Admin login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Server error occurred during authentication.'];
    }
}

function admin_logout(): void {
    unset($_SESSION['admin_id'], $_SESSION['admin_username'], $_SESSION['admin_name'], $_SESSION['admin_role']);
}
