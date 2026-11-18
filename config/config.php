<?php
/**
 * MAISON ÉLAN - Configuration File
 * Designer Fashion Boutique
 * 
 * Optimized for Hostinger Shared Hosting (PHP 8+, MySQL)
 */

// Prevent multiple inclusions
if (defined('MAISON_CONFIG_LOADED')) {
    return;
}
define('MAISON_CONFIG_LOADED', true);

// Error Reporting (Set to 0 in production on Hostinger)
$is_development = false;
if (isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'])) {
    $is_development = true;
}

if ($is_development) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Session Security Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

// Brand Information
define('SITE_NAME', 'MAISON ÉLAN');
define('SITE_TAGLINE', 'Designed to Be Remembered.');
define('SITE_DESCRIPTION', 'Luxury Indian designer fashion boutique featuring bespoke sarees, evening wear, anarkalis, festive ensembles, and curated bridal couture.');

// Database Credentials (Update with Hostinger MySQL credentials)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'maison_elan');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

// Dynamic Base URL Detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$base_path = ($script_name === '/' || $script_name === '.') ? '' : $script_name;
// Strip subfolders like /admin or /api to get root
$base_path = preg_replace('/(\/admin|\/api|\/includes).*$/', '', $base_path);
define('BASE_URL', rtrim($protocol . $host . $base_path, '/'));
define('ROOT_PATH', realpath(__DIR__ . '/..'));

// Uploads Path
define('UPLOAD_DIR', ROOT_PATH . '/assets/uploads/products/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/products/');

// E-commerce Currency & Pricing Rules
define('CURRENCY_SYMBOL', '₹');
define('CURRENCY_CODE', 'INR');
define('FREE_SHIPPING_THRESHOLD', 5000); // Free shipping above ₹5,000
define('FLAT_SHIPPING_RATE', 250);       // Flat shipping rate ₹250 for orders below threshold

// Razorpay Payment Gateway Configuration
// Replace with your Live / Test credentials from Razorpay Dashboard
define('RAZORPAY_KEY_ID', getenv('RAZORPAY_KEY_ID') ?: 'rzp_test_placeholder_key');
define('RAZORPAY_KEY_SECRET', getenv('RAZORPAY_KEY_SECRET') ?: 'placeholder_secret_key');
define('RAZORPAY_SANDBOX', true); // Set to false in production

// Boutique Contact Details
define('STORE_EMAIL', 'concierge@maisonelan.com');
define('STORE_PHONE', '+91 98765 43210');
define('STORE_WHATSAPP', '+919876543210');
define('STORE_ADDRESS', '42 Heritage Boulevard, Chanakyapuri, New Delhi 110021, India');
define('STORE_HOURS', 'Mon – Sat: 10:30 AM – 8:00 PM IST');

// Social Media Links
define('SOCIAL_INSTAGRAM', 'https://instagram.com/maisonelan');
define('SOCIAL_FACEBOOK', 'https://facebook.com/maisonelan');
define('SOCIAL_PINTEREST', 'https://pinterest.com/maisonelan');

// Timezone
date_default_timezone_set('Asia/Kolkata');
