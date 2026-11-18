<?php
/**
 * MAISON ÉLAN - Wishlist AJAX API
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

$db = getDB();
$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$sessionId = get_session_id();
$action = 'added';

if (in_array($productId, $_SESSION['wishlist'])) {
    // Remove
    $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$productId]));
    $action = 'removed';

    if ($db) {
        try {
            if ($userId) {
                $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")->execute([$userId, $productId]);
            } else {
                $db->prepare("DELETE FROM wishlist WHERE session_id = ? AND product_id = ?")->execute([$sessionId, $productId]);
            }
        } catch (\Exception $e) {}
    }
} else {
    // Add
    $_SESSION['wishlist'][] = $productId;
    $action = 'added';

    if ($db) {
        try {
            if ($userId) {
                $check = $db->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
                $check->execute([$userId, $productId]);
                if (!$check->fetch()) {
                    $ins = $db->prepare("INSERT INTO wishlist (session_id, user_id, product_id, created_at) VALUES (?, ?, ?, NOW())");
                    $ins->execute([$sessionId, $userId, $productId]);
                }
            } else {
                $check = $db->prepare("SELECT id FROM wishlist WHERE session_id = ? AND product_id = ?");
                $check->execute([$sessionId, $productId]);
                if (!$check->fetch()) {
                    $ins = $db->prepare("INSERT INTO wishlist (session_id, user_id, product_id, created_at) VALUES (?, NULL, ?, NOW())");
                    $ins->execute([$sessionId, $productId]);
                }
            }
        } catch (\Exception $e) {}
    }
}

echo json_encode([
    'success' => true,
    'action' => $action,
    'message' => $action === 'added' ? 'Added to your wishlist.' : 'Removed from your wishlist.',
    'wishlist_count' => count(get_wishlist_ids())
]);
