<?php
/**
 * MAISON ÉLAN - Local Development Server Router
 * Simulates Apache .htaccess mod_rewrite for PHP's built-in web server
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$filePath = __DIR__ . $uri;

// If static file exists, serve it directly
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// Clean URL Rewrites
if ($uri === '/shop' || $uri === '/shop/') {
    require __DIR__ . '/shop.php';
    exit;
}

if (preg_match('#^/product/([a-zA-Z0-9_-]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/product.php';
    exit;
}

if (preg_match('#^/category/([a-zA-Z0-9_-]+)/?$#', $uri, $m)) {
    $_GET['category'] = $m[1];
    require __DIR__ . '/shop.php';
    exit;
}

if (preg_match('#^/collection/([a-zA-Z0-9_-]+)/?$#', $uri, $m)) {
    $_GET['collection'] = $m[1];
    require __DIR__ . '/shop.php';
    exit;
}

// Clean extensionless PHP pages (e.g. /cart, /checkout, /login)
if (file_exists(__DIR__ . $uri . '.php')) {
    require __DIR__ . $uri . '.php';
    exit;
}

// Admin index
if ($uri === '/admin' || $uri === '/admin/') {
    require __DIR__ . '/admin/index.php';
    exit;
}

// Default to index.php
require __DIR__ . '/index.php';
