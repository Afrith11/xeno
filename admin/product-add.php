<?php
/**
 * MAISON ÉLAN - Admin Add Product
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$adminTitle = "Add Silhouette";
$pageHeading = "Commission New Silhouette";

$db = getDB();
$categories = [];
if ($db) {
    $categories = $db->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security session token. Please reload.';
    }

    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $sku = trim($_POST['sku'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $salePrice = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stock = max(0, (int)($_POST['stock_quantity'] ?? 10));
    $sizes = trim($_POST['sizes'] ?? 'Free Size');
    $colors = trim($_POST['colors'] ?? 'Ivory');
    $shortDesc = trim($_POST['short_description'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $fabricCare = trim($_POST['fabric_care'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isBestseller = isset($_POST['is_bestseller']) ? 1 : 0;
    $isNew = isset($_POST['is_new']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    $primaryImageUrl = trim($_POST['primary_image_url'] ?? '');
    $hoverImageUrl = trim($_POST['hover_image_url'] ?? '');

    // Validation
    if (empty($name)) {
        $errors[] = 'Product name is required.';
    }
    if ($categoryId <= 0) {
        $errors[] = 'Please select a valid category.';
    }
    if ($price <= 0) {
        $errors[] = 'Regular price must be greater than zero.';
    }
    if (empty($sku)) {
        $sku = 'ME-' . strtoupper(substr(slugify($name), 0, 3)) . '-' . rand(100, 999);
    }
    if (empty($primaryImageUrl)) {
        $primaryImageUrl = 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80';
    }

    // Handle file upload if present
    if (!empty($_FILES['image_file']['name'])) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        $fileName = $_FILES['image_file']['name'];
        $fileTmp = $_FILES['image_file']['tmp_name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExts) && getimagesize($fileTmp)) {
            $newFileName = 'prod_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $destPath = UPLOAD_DIR . $newFileName;
            if (move_uploaded_file($fileTmp, $destPath)) {
                $primaryImageUrl = UPLOAD_URL . $newFileName;
            }
        }
    }

    if (empty($errors) && $db) {
        try {
            $db->beginTransaction();

            $slug = slugify($name);
            // Ensure unique slug
            $checkSlug = $db->prepare("SELECT id FROM products WHERE slug = ?");
            $checkSlug->execute([$slug]);
            if ($checkSlug->fetch()) {
                $slug .= '-' . rand(10, 99);
            }

            $stmt = $db->prepare("INSERT INTO products 
                (category_id, name, slug, sku, short_description, description, price, sale_price, 
                 stock_quantity, sizes, colors, fabric_care, is_featured, is_bestseller, is_new, is_active, rating, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 5.0, NOW())");
            
            $stmt->execute([
                $categoryId,
                $name,
                $slug,
                $sku,
                $shortDesc,
                $description,
                $price,
                $salePrice,
                $stock,
                $sizes,
                $colors,
                $fabricCare,
                $isFeatured,
                $isBestseller,
                $isNew,
                $isActive
            ]);

            $newProdId = (int)$db->lastInsertId();

            // Insert primary image
            $imgStmt = $db->prepare("INSERT INTO product_images (product_id, image_url, alt_text, is_primary, is_hover, sort_order) VALUES (?, ?, ?, 1, 0, 1)");
            $imgStmt->execute([$newProdId, $primaryImageUrl, $name]);

            // Insert hover image if provided
            if (!empty($hoverImageUrl)) {
                $hoverStmt = $db->prepare("INSERT INTO product_images (product_id, image_url, alt_text, is_primary, is_hover, sort_order) VALUES (?, ?, ?, 0, 1, 2)");
                $hoverStmt->execute([$newProdId, $hoverImageUrl, $name . ' Alternate']);
            }

            $db->commit();
            set_flash_message('success', 'Silhouette ' . e($name) . ' added successfully to the atelier catalog.');
            redirect(BASE_URL . '/admin/products.php');

        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $errors[] = 'Failed to create product: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-actions-row">
    <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-outline btn-sm">&larr; Return to Products</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="auth-alert error">
        <ul>
            <?php foreach ($errors as $er): ?>
                <li><?= e($er) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-panel-card">
    <form action="<?= BASE_URL ?>/admin/product-add.php" method="POST" enctype="multipart/form-data" class="admin-form">
        <?= csrf_field() ?>

        <div class="admin-form-grid">
            <!-- Left Primary Details -->
            <div class="form-col-wide">
                <div class="form-group">
                    <label for="prodName" class="form-label">Silhouette Name *</label>
                    <input type="text" id="prodName" name="name" class="form-control" placeholder="e.g. Ivory Silk Draped Saree" value="<?= e($_POST['name'] ?? '') ?>" required>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="prodCategory" class="form-label">Collection Category *</label>
                        <select id="prodCategory" name="category_id" class="form-control" required>
                            <option value="">Select Category...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" <?= (isset($_POST['category_id']) && (int)$_POST['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="prodSku" class="form-label">SKU Identifier (Optional)</label>
                        <input type="text" id="prodSku" name="sku" class="form-control" placeholder="e.g. ME-SAR-015" value="<?= e($_POST['sku'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="prodShortDesc" class="form-label">Short Editorial Summary</label>
                    <textarea id="prodShortDesc" name="short_description" class="form-control" rows="2" placeholder="Brief one-sentence highlights for cards..."><?= e($_POST['short_description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prodDesc" class="form-label">Full Atelier Description</label>
                    <textarea id="prodDesc" name="description" class="form-control" rows="6" placeholder="Comprehensive silhouette description, draping instructions, artisan history..."><?= e($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prodFabricCare" class="form-label">Fabric & Care Instructions</label>
                    <textarea id="prodFabricCare" name="fabric_care" class="form-control" rows="3" placeholder="Dry clean only. Store wrapped in soft muslin cloth..."><?= e($_POST['fabric_care'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Right Pricing, Stock, Flags & Media -->
            <div class="form-col-side">
                <div class="form-side-card">
                    <h4 class="side-card-title">Pricing & Inventory</h4>

                    <div class="form-group">
                        <label for="prodPrice" class="form-label">Regular Price (₹) *</label>
                        <input type="number" step="0.01" id="prodPrice" name="price" class="form-control" placeholder="8490.00" value="<?= e($_POST['price'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="prodSalePrice" class="form-label">Sale Price (₹, optional)</label>
                        <input type="number" step="0.01" id="prodSalePrice" name="sale_price" class="form-control" placeholder="Leave empty if regular" value="<?= e($_POST['sale_price'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="prodStock" class="form-label">Atelier Stock Quantity</label>
                        <input type="number" id="prodStock" name="stock_quantity" class="form-control" value="<?= e($_POST['stock_quantity'] ?? '10') ?>" min="0">
                    </div>
                </div>

                <div class="form-side-card">
                    <h4 class="side-card-title">Sizes & Palette</h4>
                    
                    <div class="form-group">
                        <label for="prodSizes" class="form-label">Available Sizes (Comma-separated)</label>
                        <input type="text" id="prodSizes" name="sizes" class="form-control" value="<?= e($_POST['sizes'] ?? 'XS,S,M,L,XL,Free Size') ?>">
                    </div>

                    <div class="form-group">
                        <label for="prodColors" class="form-label">Colors (Comma-separated)</label>
                        <input type="text" id="prodColors" name="colors" class="form-control" value="<?= e($_POST['colors'] ?? 'Ivory,Champagne Gold') ?>">
                    </div>
                </div>

                <div class="form-side-card">
                    <h4 class="side-card-title">Editorial Flags</h4>

                    <label class="admin-checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" <?= isset($_POST['is_featured']) ? 'checked' : '' ?>>
                        <span>Mark as Featured</span>
                    </label>

                    <label class="admin-checkbox-label">
                        <input type="checkbox" name="is_bestseller" value="1" <?= isset($_POST['is_bestseller']) ? 'checked' : '' ?>>
                        <span>Mark as Bestseller</span>
                    </label>

                    <label class="admin-checkbox-label">
                        <input type="checkbox" name="is_new" value="1" <?= (!isset($_POST) || isset($_POST['is_new'])) ? 'checked' : '' ?>>
                        <span>Mark as New Arrival 2026</span>
                    </label>

                    <label class="admin-checkbox-label">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span>Active in Online Boutique</span>
                    </label>
                </div>

                <div class="form-side-card">
                    <h4 class="side-card-title">Photography & Imagery</h4>

                    <div class="form-group">
                        <label for="prodPrimaryImg" class="form-label">Primary Image URL *</label>
                        <input type="url" id="prodPrimaryImg" name="primary_image_url" class="form-control" placeholder="https://images.unsplash.com/..." value="<?= e($_POST['primary_image_url'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="prodHoverImg" class="form-label">Secondary Hover Image URL</label>
                        <input type="url" id="prodHoverImg" name="hover_image_url" class="form-control" placeholder="https://images.unsplash.com/..." value="<?= e($_POST['hover_image_url'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="prodImageFile" class="form-label">Or Upload File from Computer</label>
                        <input type="file" id="prodImageFile" name="image_file" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="form-side-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">PUBLISH SILHOUETTE</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
