<?php
/**
 * MAISON ÉLAN - Admin Logout
 */

require_once __DIR__ . '/includes/admin-auth.php';

admin_logout();
redirect(BASE_URL . '/admin/login.php');
