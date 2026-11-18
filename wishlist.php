<?php
/**
 * MAISON ÉLAN - Wishlist Page
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/cart-functions.php';

// Handle remove from wishlist action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $removeId = (int)$_POST['remove_id'];
    
    // Remove from session
    if (!empty($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$removeId]));
    }
    
    // Remove from DB if logged in
    if (!empty($_SESSION['user_id'])) {
        $db = getDB();
        if ($db) {
            try {
                $del = $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
                $del->execute([$_SESSION['user_id'], $removeId]);
            } catch (\Exception $e) {}
        }
    }

    set_flash_message('info', 'Piece removed from your wishlist.');
    redirect(BASE_URL . '/wishlist.php');
}

$wishlistIds = get_wishlist_ids();
$wishlistProducts = [];

if (!empty($wishlistIds)) {
    foreach ($wishlistIds as $id) {
        $p = get_product($id);
        if ($p) {
            $wishlistProducts[] = $p;
        }
    }
}

$pageTitle = "My Wishlist (" . count($wishlistProducts) . ") — Maison Élan Haute Couture";
$pageDescription = "Review your saved designer fashion pieces and move them to your shopping bag.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="wishlist-page-container site-container">
    <div class="account-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">My Wishlist</span>
        </nav>
        <h1 class="account-title">SAVED SILHOUETTES</h1>
        <p class="account-sub">Your personal curation of bespoke sarees, gowns, and festive couture.</p>
    </div>

    <?php if (empty($wishlistProducts)): ?>
        <div class="catalog-empty-state">
            <span class="empty-icon">♡</span>
            <h3 class="empty-title">Your Wishlist is Empty</h3>
            <p class="empty-lead">Explore our collections and tap the heart icon on any silhouette to curate your private wishlist.</p>
            <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary">Discover Silhouettes</a>
        </div>
    <?php else: ?>
        <div class="products-editorial-grid wishlist-grid">
            <?php foreach ($wishlistProducts as $product): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
