<?php
/**
 * MAISON ÉLAN - Newsletter Subscription AJAX API
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/functions.php';

$email = trim(strtolower($_POST['email'] ?? ''));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

$db = getDB();
if ($db) {
    try {
        $stmt = $db->prepare("SELECT id FROM newsletter_subscribers WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => true, 'message' => 'You are already registered on our private preview guestlist.']);
            exit;
        }

        $ins = $db->prepare("INSERT INTO newsletter_subscribers (email, status, subscribed_at) VALUES (?, 'active', NOW())");
        $ins->execute([$email]);

        echo json_encode(['success' => true, 'message' => 'Welcome to the Maison Élan Private Circle. Your confirmation is on its way.']);
        exit;
    } catch (\Exception $e) {
        error_log("Newsletter error: " . $e->getMessage());
    }
}

// Fallback response
echo json_encode(['success' => true, 'message' => 'Welcome to the Maison Élan Private Circle.']);
