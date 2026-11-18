<?php
/**
 * MAISON ÉLAN - Live Search AJAX API
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/functions.php';

$query = trim($_GET['q'] ?? '');

if (strlen($query) < 2) {
    echo json_encode(['results' => [], 'count' => 0]);
    exit;
}

$products = get_products([
    'search' => $query,
    'limit' => 6
]);

$results = [];
foreach ($products as $p) {
    $price = (float)$p['price'];
    $salePrice = !empty($p['sale_price']) && $p['sale_price'] > 0 ? (float)$p['sale_price'] : null;
    $effectivePrice = $salePrice ?: $price;

    $results[] = [
        'id' => (int)$p['id'],
        'name' => $p['name'],
        'slug' => $p['slug'],
        'url' => BASE_URL . '/product.php?slug=' . urlencode($p['slug']),
        'category' => $p['category_name'] ?? 'Haute Couture',
        'price' => format_price($effectivePrice),
        'image' => $p['primary_image'] ?? 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=400&q=80'
    ];
}

echo json_encode([
    'results' => $results,
    'count' => count($results),
    'view_all_url' => BASE_URL . '/shop.php?search=' . urlencode($query)
]);
