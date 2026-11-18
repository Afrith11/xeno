<?php
/**
 * MAISON ÉLAN - Admin Products List
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$adminTitle = "Products Management";
$pageHeading = "Atelier Silhouettes Catalog";

$db = getDB();
$search = trim($_GET['search'] ?? '');
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$products = [];
$categories = [];

if ($db) {
    try {
        $catStmt = $db->query("SELECT id, name FROM categories ORDER BY name ASC");
        $categories = $catStmt->fetchAll();

        $sql = "SELECT p.*, c.name as category_name,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        if ($categoryFilter > 0) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryFilter;
        }

        $sql .= " ORDER BY p.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

    } catch (\Exception $e) {
        set_flash_message('error', 'Error fetching products: ' . $e->getMessage());
    }
} else {
    $products = get_sample_products();
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-actions-row">
    <div class="search-filter-controls">
        <form action="<?= BASE_URL ?>/admin/products.php" method="GET" class="admin-filter-form">
            <input type="text" name="search" class="admin-input-search" placeholder="Search name or SKU..." value="<?= e($search) ?>">
            
            <select name="category" class="admin-select" onchange="this.form.submit()">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>" <?= $categoryFilter === (int)$cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
            <?php if (!empty($search) || $categoryFilter > 0): ?>
                <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-text btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="action-btn-group">
        <a href="<?= BASE_URL ?>/admin/product-add.php" class="btn btn-primary">+ Add New Silhouette</a>
    </div>
</div>

<div class="admin-panel-card">
    <div class="admin-table-responsive">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Silhouette Name & SKU</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Highlights</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="table-thumb-cell">
                                <img src="<?= e($p['primary_image'] ?? 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=150&q=80') ?>" alt="<?= e($p['name']) ?>" class="admin-prod-thumb">
                            </td>
                            <td>
                                <strong><?= e($p['name']) ?></strong>
                                <span class="table-subdate">SKU: <code><?= e($p['sku']) ?></code></span>
                            </td>
                            <td><?= e($p['category_name'] ?? 'General') ?></td>
                            <td>
                                <strong><?= format_price($p['price']) ?></strong>
                                <?php if (!empty($p['sale_price']) && $p['sale_price'] > 0): ?>
                                    <span class="table-subdate text-danger">Sale: <?= format_price($p['sale_price']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="stock-badge <?= (int)$p['stock_quantity'] <= 5 ? 'critical' : 'normal' ?>">
                                    <?= (int)$p['stock_quantity'] ?> units
                                </span>
                            </td>
                            <td>
                                <div class="flags-tags-row">
                                    <?php if (!empty($p['is_featured'])): ?><span class="badge badge-featured">Featured</span><?php endif; ?>
                                    <?php if (!empty($p['is_bestseller'])): ?><span class="badge badge-bestseller">Bestseller</span><?php endif; ?>
                                    <?php if (!empty($p['is_new'])): ?><span class="badge badge-new">New</span><?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-pill status-<?= !empty($p['is_active']) ? 'confirmed' : 'cancelled' ?>">
                                    <?= !empty($p['is_active']) ? 'Active' : 'Draft' ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-row-actions">
                                    <a href="<?= BASE_URL ?>/product.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="btn-table-action" title="View in boutique">👁</a>
                                    <a href="<?= BASE_URL ?>/admin/product-edit.php?id=<?= (int)$p['id'] ?>" class="btn-table-action edit" title="Edit silhouette">✏</a>
                                    <a href="<?= BASE_URL ?>/admin/product-delete.php?id=<?= (int)$p['id'] ?>" class="btn-table-action delete" onclick="return confirm('Are you sure you want to retire this silhouette from the atelier?');" title="Delete silhouette">🗑</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">No products found matching query.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
