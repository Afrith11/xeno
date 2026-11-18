<?php
/**
 * MAISON ÉLAN - Admin Edit Product
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($productId <= 0) {
    redirect(BASE_URL . '/admin/products.php');
}

$db = getDB();
$product = null;
$categories = [];
$productImages = [];

if ($db) {
    $categories = $db->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
    
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        $imgStmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC");
        $imgStmt->execute([$productId]);
        $productImages = $imgStmt->fetchAll();
    }
}

if (!$product) {
    set_flash_message('error', 'Silhouette not found.');
    redirect(BASE_URL . '/admin/products.php');
}

$adminTitle = "Edit " . $product['name'];
$pageHeading = "Edit Silhouette: " . $product['name'];

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
    $stock = max(0, (int)($_POST['stock_quantity'] ?? 0));
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

    if (empty($name)) {
        $errors[] = 'Product name is required.';
    }
    if ($categoryId <= 0) {
        $errors[] = 'Please select a valid category.';
    }
    if ($price <= 0) {
        $errors[] = 'Regular price must be greater than zero.';
    }

    // Handle uploaded file
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

            $stmt = $db->prepare("UPDATE products SET 
                category_id = ?, name = ?, sku = ?, short_description = ?, description = ?,
                price = ?, sale_price = ?, stock_quantity = ?, sizes = ?, colors = ?,
                fabric_care = ?, is_featured = ?, is_bestseller = ?, is_new = ?, is_active = ?,
                updated_at = NOW()
                WHERE id = ?");
            
            $stmt->execute([
                $categoryId,
                $name,
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
                $isActive,
                $productId
            ]);

            // Update primary image if provided
            if (!empty($primaryImageUrl)) {
                $checkPrim = $db->prepare("SELECT id FROM product_images WHERE product_id = ? AND is_primary = 1");
                $checkPrim->execute([$productId]);
                $primRow = $checkPrim->fetch();
                if ($primRow) {
                    $db->prepare("UPDATE product_images SET image_url = ? WHERE id = ?")->execute([$primaryImageUrl, $primRow['id']]);
                } else {
                    $db->prepare("INSERT INTO product_images (product_id, image_url, alt_text, is_primary, is_hover, sort_order) VALUES (?, ?, ?, 1, 0, 1)")
                       ->execute([$productId, $primaryImageUrl, $name]);
                }
            }

            // Update hover image if provided
            if (!empty($hoverImageUrl)) {
                $checkHov = $db->prepare("SELECT id FROM product_images WHERE product_id = ? AND is_hover = 1");
                $checkHov->execute([$productId]);
                $hovRow = $checkHov->fetch();
                if ($hovRow) {
                    $db->prepare("UPDATE product_images SET image_url = ? WHERE id = ?")->execute([$hoverImageUrl, $hovRow['id']]);
                } else {
                    $db->prepare("INSERT INTO product_images (product_id, image_url, alt_text, is_primary, is_hover, sort_order) VALUES (?, ?, ?, 0, 1, 2)")
                       ->execute([$productId, $hoverImageUrl, $name . ' Hover']);
                }
            }

            $db->commit();
            set_flash_message('success', 'Silhouette changes saved successfully.');
            redirect(BASE_URL . '/admin/product-edit.php?id=' . $productId);

        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $errors[] = 'Failed to update silhouette: ' . $e->getMessage();
        }
    }
}

// Find existing primary and hover image URLs
$currentPrimaryImg = '';
$currentHoverImg = '';
foreach ($productImages as $im) {
    if ($im['is_primary']) $currentPrimaryImg = $im['image_url'];
    if ($im['is_hover']) $currentHoverImg = $im['image_url'];
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-actions-row">
    <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-outline btn-sm">&larr; Return to Products</a>
    <a href="<?= BASE_URL ?>/product.php?slug=<?= urlencode($product['slug']) ?>" target="_blank" class="btn btn-outline btn-sm">Preview in Boutique ↗</a>
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
    <form action="<?= BASE_URL ?>/admin/product-edit.php?id=<?= $productId ?>" method="POST" enctype="multipart/form-data" class="admin-form">
        <?= csrf_field() ?>

        <div class="admin-form-grid">
            <!-- Left Primary Details -->
            <div class="form-col-wide">
                <div class="form-group">
                    <label for="prodName" class="form-label">Silhouette Name *</label>
                    <input type="text" id="prodName" name="name" class="form-control" value="<?= e($product['name']) ?>" required>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="prodCategory" class="form-label">Collection Category *</label>
                        <select id="prodCategory" name="category_id" class="form-control" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" <?= (int)$product['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="prodSku" class="form-label">SKU Identifier</label>
                        <input type="text" id="prodSku" name="sku" class="form-control" value="<?= e($product['sku']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="prodShortDesc" class="form-label">Short Editorial Summary</label>
                    <textarea id="prodShortDesc" name="short_description" class="form-control" rows="2"><?= e($product['short_description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prodDesc" class="form-label">Full Atelier Description</label>
                    <textarea id="prodDesc" name="description" class="form-control" rows="6"><?= e($product['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prodFabricCare" class="form-label">Fabric & Care Instructions</label>
                    <textarea id="prodFabricCare" name="fabric_care" class="form-control" rows="3"><?= e($product['fabric_care']) ?></textarea>
                </div>
            </div>

            <!-- Right Pricing, Stock, Flags & Media -->
            <div class="form-col-side">
                <div class="form-side-card">
                    <h4 class="side-card-title">Pricing & Inventory</h4>

                    <div class="form-group">
                        <label for="prodPrice" class="form-label">Regular Price (₹) *</label>
                        <input type="number" step="0.01" id="prodPrice" name="price" class="form-control" value="<?= e($product['price']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="prodSalePrice" class="form-label">Sale Price (₹)</label>
                        <input type="number" step="0.01" id="prodSalePrice" name="sale_price" class="form-control" value="<?= e($product['sale_price'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="prodStock" class="form-label">Atelier Stock Quantity</label>
                        <input type="number" id="prodStock" name="stock_quantity" class="form-control" value="<?= (int)$product['stock_quantity'] ?>" min="0">
                    </div>
                </div>

                <div class="form-side-card">
                    <h4 class="side-card-title">Sizes & Palette</h4>
                    
                    <div class="form-group">
                        <label for="prodSizes" class="form-label">Available Sizes</label>
                        <input type="text" id="prodSizes" name="sizes" class="form-control" value="<?= e($product['sizes']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="prodColors" class="form-label">Colors</label>
                        <input type="text" id="prodColors" name="colors" class="form-control" value="<?= e($product['colors']) ?>">
                    </div>
                </div>

                <div class="form-side-card">
                    <h4 class="side-card-title">Editorial Flags</h4>

                    <label class="admin-checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
                        <span>Mark as Featured</span>
                    </label>

                    <label class="admin-checkbox-label">
                        <input type="checkbox" name="is_bestseller" value="1" <?= !empty($product['is_bestseller']) ? 'checked' : '' ?>>
                        <span>Mark as Bestseller</span>
                    </label>

                    <label class="admin-checkbox-label">
                        <input type="checkbox" name="is_new" value="1" <?= !empty($product['is_new']) ? 'checked' : '' ?>>
                        <span>Mark as New Arrival 2026</span>
                    </label>

                    <label class="admin-checkbox-label">
                        <input type="checkbox" name="is_active" value="1" <?= !empty($product['is_active']) ? 'checked' : '' ?>>
                        <span>Active in Online Boutique</span>
                    </label>
                </div>

                <div class="form-side-card">
                    <h4 class="side-card-title">Photography & Imagery</h4>

                    <div class="form-group">
                        <label for="prodPrimaryImg" class="form-label">Primary Image URL</label>
                        <input type="url" id="prodPrimaryImg" name="primary_image_url" class="form-control" value="<?= e($currentPrimaryImg) ?>">
                    </div>

                    <div class="form-group">
                        <label for="prodHoverImg" class="form-label">Secondary Hover Image URL</label>
                        <input type="url" id="prodHoverImg" name="hover_image_url" class="form-control" value="<?= e($currentHoverImg) ?>">
                    </div>

                    <div class="form-group">
                        <label for="prodImageFile" class="form-label">Upload Replacement Image</label>
                        <input type="file" id="prodImageFile" name="image_file" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="form-side-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">SAVE ATELIER CHANGES</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
