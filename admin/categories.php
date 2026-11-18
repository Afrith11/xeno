<?php
/**
 * MAISON ÉLAN - Admin Categories Management
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$adminTitle = "Categories Management";
$pageHeading = "Atelier Collections & Categories";

$db = getDB();
$errors = [];

// Handle Add / Edit Category POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security session token.';
    }

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $editId = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    if (empty($name)) {
        $errors[] = 'Category name is required.';
    }

    if (empty($errors) && $db) {
        $slug = slugify($name);
        try {
            if ($editId > 0) {
                $stmt = $db->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, image_url = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description, $imageUrl, $sortOrder, $isActive, $editId]);
                set_flash_message('success', "Category '{$name}' updated successfully.");
            } else {
                $stmt = $db->prepare("INSERT INTO categories (name, slug, description, image_url, sort_order, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $slug, $description, $imageUrl, $sortOrder, $isActive]);
                set_flash_message('success', "New category '{$name}' established.");
            }
            redirect(BASE_URL . '/admin/categories.php');
        } catch (\Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($db) {
        try {
            $db->prepare("DELETE FROM categories WHERE id = ?")->execute([$delId]);
            set_flash_message('success', 'Category removed.');
            redirect(BASE_URL . '/admin/categories.php');
        } catch (\Exception $e) {
            set_flash_message('error', 'Cannot delete category with associated products.');
        }
    }
}

// Fetch categories with product counts
$categories = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT c.*, COUNT(p.id) as product_count 
                            FROM categories c 
                            LEFT JOIN products p ON c.id = p.category_id 
                            GROUP BY c.id 
                            ORDER BY c.sort_order ASC, c.name ASC");
        $categories = $stmt->fetchAll();
    } catch (\Exception $e) {}
}

// Pre-fill form if editing
$editCat = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $eId = (int)$_GET['edit'];
    foreach ($categories as $c) {
        if ((int)$c['id'] === $eId) {
            $editCat = $c;
            break;
        }
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if (!empty($errors)): ?>
    <div class="auth-alert error">
        <ul>
            <?php foreach ($errors as $er): ?>
                <li><?= e($er) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-two-cols-layout">
    <!-- Left: Categories Table -->
    <div class="admin-panel-card col-wide">
        <div class="panel-card-header">
            <h3 class="panel-title">Active Categories (<?= count($categories) ?>)</h3>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name & Slug</th>
                        <th>Products</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <img src="<?= e($cat['image_url'] ?? 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=150&q=80') ?>" alt="<?= e($cat['name']) ?>" class="admin-prod-thumb">
                                </td>
                                <td>
                                    <strong><?= e($cat['name']) ?></strong>
                                    <span class="table-subdate">/category/<?= e($cat['slug']) ?></span>
                                </td>
                                <td><?= (int)$cat['product_count'] ?> pieces</td>
                                <td><?= (int)$cat['sort_order'] ?></td>
                                <td>
                                    <span class="status-pill status-<?= !empty($cat['is_active']) ? 'confirmed' : 'cancelled' ?>">
                                        <?= !empty($cat['is_active']) ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-row-actions">
                                        <a href="<?= BASE_URL ?>/shop.php?category=<?= urlencode($cat['slug']) ?>" target="_blank" class="btn-table-action" title="View in boutique">👁</a>
                                        <a href="<?= BASE_URL ?>/admin/categories.php?edit=<?= (int)$cat['id'] ?>" class="btn-table-action edit" title="Edit">✏</a>
                                        <a href="<?= BASE_URL ?>/admin/categories.php?delete=<?= (int)$cat['id'] ?>" class="btn-table-action delete" onclick="return confirm('Delete this category?');" title="Delete">🗑</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4">No categories configured yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Add / Edit Category Form -->
    <div class="admin-panel-card col-side">
        <div class="panel-card-header">
            <h3 class="panel-title"><?= $editCat ? 'Edit Category' : 'Add Category' ?></h3>
            <?php if ($editCat): ?>
                <a href="<?= BASE_URL ?>/admin/categories.php" class="btn btn-text btn-sm">Cancel Edit</a>
            <?php endif; ?>
        </div>

        <form action="<?= BASE_URL ?>/admin/categories.php" method="POST" class="admin-form">
            <?= csrf_field() ?>
            <?php if ($editCat): ?>
                <input type="hidden" name="edit_id" value="<?= (int)$editCat['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="catName" class="form-label">Category Name *</label>
                <input type="text" id="catName" name="name" class="form-control" placeholder="e.g. Sarees" value="<?= e($editCat['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="catDesc" class="form-label">Editorial Description</label>
                <textarea id="catDesc" name="description" class="form-control" rows="3" placeholder="Brief atelier notes..."><?= e($editCat['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="catImg" class="form-label">Banner Image URL</label>
                <input type="url" id="catImg" name="image_url" class="form-control" placeholder="https://images.unsplash.com/..." value="<?= e($editCat['image_url'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="catOrder" class="form-label">Display Sort Order</label>
                <input type="number" id="catOrder" name="sort_order" class="form-control" value="<?= (int)($editCat['sort_order'] ?? 0) ?>">
            </div>

            <label class="admin-checkbox-label">
                <input type="checkbox" name="is_active" value="1" <?= (!$editCat || !empty($editCat['is_active'])) ? 'checked' : '' ?>>
                <span>Active in Boutique Navigation</span>
            </label>

            <div class="form-actions" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary btn-block"><?= $editCat ? 'Update Category' : 'Create Category' ?></button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
