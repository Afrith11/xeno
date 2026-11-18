<?php
/**
 * MAISON ÉLAN - Customer Logout
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

customer_logout();
set_flash_message('info', 'You have been signed out. We look forward to your return.');
redirect(BASE_URL . '/login.php');
