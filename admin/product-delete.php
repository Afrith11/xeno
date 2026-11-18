<?php
/**
 * MAISON ÉLAN - Admin Delete Product
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $db = getDB();
    if ($db) {
        try {
            // Delete product (cascades to product_images)
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            set_flash_message('success', 'Silhouette retired from the atelier catalog.');
        } catch (\Exception $e) {
            set_flash_message('error', 'Could not delete product: ' . $e->getMessage());
        }
    }
}

redirect(BASE_URL . '/admin/products.php');
