<?php
/**
 * MAISON ÉLAN - Cart AJAX API
 * JSON endpoint for adding, updating, and removing bag items
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart-functions.php';

$action = $_REQUEST['action'] ?? 'get';

try {
    switch ($action) {
        case 'add':
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
            $size = isset($_POST['size']) && trim($_POST['size']) !== '' ? trim($_POST['size']) : 'Free Size';
            $color = isset($_POST['color']) ? trim($_POST['color']) : '';

            if ($productId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product identifier.']);
                exit;
            }

            $res = add_to_cart($productId, $quantity, $size, $color);
            $res['cart_summary'] = get_cart_summary();
            echo json_encode($res);
            break;

        case 'update':
            $key = $_POST['item_key'] ?? '';
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            if (empty($key)) {
                echo json_encode(['success' => false, 'message' => 'Missing item key.']);
                exit;
            }

            $res = update_cart_quantity($key, $quantity);
            echo json_encode($res);
            break;

        case 'remove':
            $key = $_POST['item_key'] ?? '';
            if (empty($key)) {
                echo json_encode(['success' => false, 'message' => 'Missing item key.']);
                exit;
            }

            $res = remove_from_cart($key);
            echo json_encode($res);
            break;

        case 'apply_coupon':
            $code = $_POST['coupon_code'] ?? '';
            $res = apply_coupon_code($code);
            echo json_encode($res);
            break;

        case 'remove_coupon':
            $res = remove_coupon_code();
            echo json_encode($res);
            break;

        case 'get':
        default:
            echo json_encode([
                'success' => true,
                'cart' => get_cart_summary()
            ]);
            break;
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected server error occurred: ' . $e->getMessage()
    ]);
}
