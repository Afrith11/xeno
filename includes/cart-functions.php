<?php
/**
 * MAISON ÉLAN - Cart Functions
 * Supports Guest Session & Database Persistent Cart
 */

require_once __DIR__ . '/functions.php';

/**
 * Initialize cart session storage
 */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/**
 * Generate unique item key based on product ID, size, and color
 */
function get_cart_item_key(int $productId, string $size = 'Free Size', string $color = ''): string {
    return md5($productId . '_' . trim(strtolower($size)) . '_' . trim(strtolower($color)));
}

/**
 * Add product to cart
 */
function add_to_cart(int $productId, int $quantity = 1, string $size = 'Free Size', string $color = ''): array {
    $product = get_product($productId);
    if (!$product) {
        return ['success' => false, 'message' => 'Product not found.'];
    }

    if ($product['stock_quantity'] < $quantity) {
        return ['success' => false, 'message' => 'Insufficient stock available. Only ' . $product['stock_quantity'] . ' piece(s) in atelier.'];
    }

    $key = get_cart_item_key($productId, $size, $color);

    if (isset($_SESSION['cart'][$key])) {
        $newQty = $_SESSION['cart'][$key]['quantity'] + $quantity;
        if ($newQty > $product['stock_quantity']) {
            return ['success' => false, 'message' => 'Maximum available quantity already in your shopping bag.'];
        }
        $_SESSION['cart'][$key]['quantity'] = $newQty;
    } else {
        $price = !empty($product['sale_price']) && $product['sale_price'] > 0 ? (float)$product['sale_price'] : (float)$product['price'];
        $_SESSION['cart'][$key] = [
            'key' => $key,
            'product_id' => $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'size' => $size,
            'color' => $color,
            'price' => $price,
            'regular_price' => (float)$product['price'],
            'quantity' => $quantity,
            'image' => $product['primary_image'] ?? ($product['images'][0]['image_url'] ?? '')
        ];
    }

    // Sync to database if user is logged in
    if (!empty($_SESSION['user_id'])) {
        $db = getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ? AND size = ?");
                $stmt->execute([$_SESSION['user_id'], $productId, $size]);
                $row = $stmt->fetch();
                if ($row) {
                    $upd = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                    $upd->execute([$_SESSION['cart'][$key]['quantity'], $row['id']]);
                } else {
                    $ins = $db->prepare("INSERT INTO cart (session_id, user_id, product_id, size, color, quantity) VALUES (?, ?, ?, ?, ?, ?)");
                    $ins->execute([get_session_id(), $_SESSION['user_id'], $productId, $size, $color, $_SESSION['cart'][$key]['quantity']]);
                }
            } catch (\Exception $e) {
                error_log("DB Cart sync error: " . $e->getMessage());
            }
        }
    }

    return [
        'success' => true,
        'message' => 'Added to your shopping bag.',
        'cart_count' => get_cart_count(),
        'item' => $_SESSION['cart'][$key]
    ];
}

/**
 * Update cart item quantity
 */
function update_cart_quantity(string $key, int $quantity): array {
    if (!isset($_SESSION['cart'][$key])) {
        return ['success' => false, 'message' => 'Item not in cart.'];
    }

    if ($quantity <= 0) {
        return remove_from_cart($key);
    }

    $item = $_SESSION['cart'][$key];
    $product = get_product($item['product_id']);
    if ($product && $quantity > $product['stock_quantity']) {
        return ['success' => false, 'message' => 'Only ' . $product['stock_quantity'] . ' piece(s) available in atelier.'];
    }

    $_SESSION['cart'][$key]['quantity'] = $quantity;

    // Sync DB
    if (!empty($_SESSION['user_id'])) {
        $db = getDB();
        if ($db) {
            try {
                $upd = $db->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ? AND size = ?");
                $upd->execute([$quantity, $_SESSION['user_id'], $item['product_id'], $item['size']]);
            } catch (\Exception $e) {}
        }
    }

    return ['success' => true, 'message' => 'Bag updated successfully.', 'cart' => get_cart_summary()];
}

/**
 * Remove item from cart
 */
function remove_from_cart(string $key): array {
    if (isset($_SESSION['cart'][$key])) {
        $item = $_SESSION['cart'][$key];
        unset($_SESSION['cart'][$key]);

        // Sync DB
        if (!empty($_SESSION['user_id'])) {
            $db = getDB();
            if ($db) {
                try {
                    $del = $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ? AND size = ?");
                    $del->execute([$_SESSION['user_id'], $item['product_id'], $item['size']]);
                } catch (\Exception $e) {}
            }
        }

        return ['success' => true, 'message' => 'Item removed from bag.', 'cart' => get_cart_summary()];
    }

    return ['success' => false, 'message' => 'Item not found in bag.'];
}

/**
 * Clear entire cart
 */
function clear_cart(): void {
    $_SESSION['cart'] = [];
    unset($_SESSION['coupon']);

    if (!empty($_SESSION['user_id'])) {
        $db = getDB();
        if ($db) {
            try {
                $del = $db->prepare("DELETE FROM cart WHERE user_id = ?");
                $del->execute([$_SESSION['user_id']]);
            } catch (\Exception $e) {}
        }
    }
}

/**
 * Total item count in cart
 */
function get_cart_count(): int {
    $count = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += (int)($item['quantity'] ?? 1);
        }
    }
    return $count;
}

/**
 * Get full cart data with calculations
 */
function get_cart_summary(): array {
    $items = $_SESSION['cart'] ?? [];
    $subtotal = 0.0;
    
    foreach ($items as &$item) {
        $lineTotal = (float)$item['price'] * (int)$item['quantity'];
        $item['line_total'] = $lineTotal;
        $subtotal += $lineTotal;
    }

    // Shipping calculation
    $shipping_fee = 0.0;
    if ($subtotal > 0) {
        if ($subtotal < FREE_SHIPPING_THRESHOLD) {
            $shipping_fee = (float)FLAT_SHIPPING_RATE;
        } else {
            $shipping_fee = 0.0; // Free delivery
        }
    }

    // Coupon discount calculation
    $discount_amount = 0.0;
    $coupon = $_SESSION['coupon'] ?? null;
    if ($coupon && $subtotal >= ($coupon['min_order_value'] ?? 0)) {
        if ($coupon['discount_type'] === 'percentage') {
            $calc = ($subtotal * (float)$coupon['discount_value']) / 100;
            if (!empty($coupon['max_discount']) && $calc > (float)$coupon['max_discount']) {
                $calc = (float)$coupon['max_discount'];
            }
            $discount_amount = $calc;
        } else {
            $discount_amount = min((float)$coupon['discount_value'], $subtotal);
        }
    } else {
        // Invalidate coupon if minimum order value not reached
        if ($coupon && $subtotal < ($coupon['min_order_value'] ?? 0)) {
            unset($_SESSION['coupon']);
            $coupon = null;
        }
    }

    $grand_total = max(0, ($subtotal - $discount_amount) + $shipping_fee);

    return [
        'items' => array_values($items),
        'item_count' => get_cart_count(),
        'subtotal' => $subtotal,
        'shipping_fee' => $shipping_fee,
        'free_shipping_eligible' => $subtotal >= FREE_SHIPPING_THRESHOLD,
        'amount_to_free_shipping' => max(0, FREE_SHIPPING_THRESHOLD - $subtotal),
        'discount_amount' => $discount_amount,
        'coupon' => $coupon,
        'grand_total' => $grand_total,
        'formatted' => [
            'subtotal' => format_price($subtotal),
            'shipping_fee' => $shipping_fee > 0 ? format_price($shipping_fee) : 'Complimentary',
            'discount_amount' => format_price($discount_amount),
            'grand_total' => format_price($grand_total)
        ]
    ];
}

/**
 * Apply coupon code
 */
function apply_coupon_code(string $code): array {
    $code = strtoupper(trim($code));
    if (empty($code)) {
        return ['success' => false, 'message' => 'Please enter a valid coupon code.'];
    }

    $db = getDB();
    $coupon = null;

    if ($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND valid_from <= CURDATE() AND valid_until >= CURDATE() LIMIT 1");
            $stmt->execute([$code]);
            $coupon = $stmt->fetch();
        } catch (\Exception $e) {}
    } else {
        // Fallback coupons
        if ($code === 'ELAN10') {
            $coupon = [
                'code' => 'ELAN10',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_order_value' => 3000,
                'max_discount' => 2000
            ];
        } elseif ($code === 'WELCOME500') {
            $coupon = [
                'code' => 'WELCOME500',
                'discount_type' => 'fixed',
                'discount_value' => 500,
                'min_order_value' => 4000,
                'max_discount' => 500
            ];
        }
    }

    if (!$coupon) {
        return ['success' => false, 'message' => 'Invalid or expired boutique privilege code.'];
    }

    $summary = get_cart_summary();
    if ($summary['subtotal'] < (float)$coupon['min_order_value']) {
        return [
            'success' => false,
            'message' => 'Privilege code ' . $code . ' requires a minimum bag value of ' . format_price($coupon['min_order_value']) . '.'
        ];
    }

    $_SESSION['coupon'] = $coupon;
    return [
        'success' => true,
        'message' => 'Privilege code ' . $code . ' applied successfully!',
        'cart' => get_cart_summary()
    ];
}

/**
 * Remove active coupon
 */
function remove_coupon_code(): array {
    unset($_SESSION['coupon']);
    return [
        'success' => true,
        'message' => 'Privilege code removed.',
        'cart' => get_cart_summary()
    ];
}
