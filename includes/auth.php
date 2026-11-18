<?php
/**
 * MAISON ÉLAN - Authentication System
 * Customer Registration, Login, Session Management, Security
 */

require_once __DIR__ . '/functions.php';

/**
 * Check if customer is authenticated
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Get currently authenticated customer
 */
function get_logged_in_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }

    $db = getDB();
    if (!$db) {
        return $_SESSION['user_data'] ?? null;
    }

    try {
        $stmt = $db->prepare("SELECT id, full_name, email, phone, address, city, state, pincode, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_data'] = $user;
            return $user;
        }
    } catch (\Exception $e) {
        error_log("Error fetching user: " . $e->getMessage());
    }

    return $_SESSION['user_data'] ?? null;
}

/**
 * Enforce authentication on protected customer pages
 */
function require_login(string $redirect_after = ''): void {
    if (!is_logged_in()) {
        if (!empty($redirect_after)) {
            $_SESSION['redirect_after_login'] = $redirect_after;
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'account.php';
        }
        set_flash_message('info', 'Please sign in to access your boutique account.');
        redirect(BASE_URL . '/login.php');
    }
}

/**
 * Authenticate customer login
 */
function customer_login(string $email, string $password): array {
    $email = trim(strtolower($email));
    
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Please provide both email address and password.'];
    }

    $db = getDB();
    if (!$db) {
        // Fallback for testing without DB
        if ($email === 'aanya@example.com' && $password === 'Customer@123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Aanya Singhania';
            $_SESSION['user_email'] = 'aanya@example.com';
            $_SESSION['user_data'] = [
                'id' => 1,
                'full_name' => 'Aanya Singhania',
                'email' => 'aanya@example.com',
                'phone' => '+91 98200 12345',
                'city' => 'Mumbai'
            ];
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Database connection unavailable. Please check configuration.'];
    }

    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            unset($user['password']);
            $_SESSION['user_data'] = $user;

            // Merge guest cart with user cart
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    try {
                        $check = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND size = ?");
                        $check->execute([$user['id'], $item['product_id'], $item['size']]);
                        $existing = $check->fetch();

                        if ($existing) {
                            $upd = $db->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
                            $upd->execute([$item['quantity'], $existing['id']]);
                        } else {
                            $ins = $db->prepare("INSERT INTO cart (session_id, user_id, product_id, size, color, quantity) VALUES (?, ?, ?, ?, ?, ?)");
                            $ins->execute([get_session_id(), $user['id'], $item['product_id'], $item['size'], $item['color'] ?? '', $item['quantity']]);
                        }
                    } catch (\Exception $e) {}
                }
            }

            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Invalid email address or password.'];
        }
    } catch (\Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'An unexpected error occurred during authentication.'];
    }
}

/**
 * Register new customer
 */
function customer_register(array $data): array {
    $full_name = trim($data['full_name'] ?? '');
    $email = trim(strtolower($data['email'] ?? ''));
    $password = $data['password'] ?? '';
    $phone = trim($data['phone'] ?? '');

    if (empty($full_name) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Please fill in all required fields (Name, Email, and Password).'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Please provide a valid email address.'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters in length.'];
    }

    $db = getDB();
    if (!$db) {
        return ['success' => false, 'message' => 'Database connection unavailable.'];
    }

    try {
        // Check if email already registered
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'An account with this email address already exists.'];
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $insert = $db->prepare("INSERT INTO users (full_name, email, password, phone, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insert->execute([$full_name, $email, $hashed_password, $phone]);

        $userId = (int)$db->lastInsertId();

        // Auto login
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $full_name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_data'] = [
            'id' => $userId,
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone
        ];

        return ['success' => true];
    } catch (\Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

/**
 * Customer logout
 */
function customer_logout(): void {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_data']);
    // Reset guest session id
    $_SESSION['guest_session_id'] = 'guest_' . bin2hex(random_bytes(16));
}
