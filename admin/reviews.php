<?php
/**
 * MAISON ÉLAN - Admin Reviews Moderation
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$adminTitle = "Reviews Moderation";
$pageHeading = "Client Testimonials & Feedback";

$db = getDB();

// Handle Toggle Approval
if (isset($_GET['toggle']) && is_numeric($_GET['toggle']) && $db) {
    try {
        $rId = (int)$_GET['toggle'];
        $db->prepare("UPDATE reviews SET is_approved = NOT is_approved WHERE id = ?")->execute([$rId]);
        set_flash_message('success', 'Review approval status updated.');
        redirect(BASE_URL . '/admin/reviews.php');
    } catch (\Exception $e) {}
}

// Handle Delete Review
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && $db) {
    try {
        $db->prepare("DELETE FROM reviews WHERE id = ?")->execute([(int)$_GET['delete']]);
        set_flash_message('success', 'Review deleted.');
        redirect(BASE_URL . '/admin/reviews.php');
    } catch (\Exception $e) {}
}

$reviews = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT r.*, p.name as product_name, p.slug as product_slug 
                            FROM reviews r 
                            JOIN products p ON r.product_id = p.id 
                            ORDER BY r.id DESC");
        $reviews = $stmt->fetchAll();
    } catch (\Exception $e) {}
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-panel-card">
    <div class="admin-table-responsive">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Silhouette</th>
                    <th>Patron & Location</th>
                    <th>Rating</th>
                    <th>Review Content</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $rev): ?>
                        <tr>
                            <td>
                                <strong><a href="<?= BASE_URL ?>/product.php?slug=<?= urlencode($rev['product_slug']) ?>" target="_blank"><?= e($rev['product_name']) ?></a></strong>
                            </td>
                            <td>
                                <div><strong><?= e($rev['customer_name']) ?></strong></div>
                                <span class="table-subdate"><?= e($rev['customer_city'] ?? 'India') ?></span>
                            </td>
                            <td>
                                <span style="color:var(--gold); font-size:16px;">
                                    <?= str_repeat('★', (int)$rev['rating']) ?><?= str_repeat('☆', 5 - (int)$rev['rating']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($rev['title'])): ?>
                                    <div><strong>“<?= e($rev['title']) ?>”</strong></div>
                                <?php endif; ?>
                                <p class="admin-review-text"><?= e($rev['comment']) ?></p>
                            </td>
                            <td><?= date('M d, Y', strtotime($rev['created_at'])) ?></td>
                            <td>
                                <span class="status-pill status-<?= !empty($rev['is_approved']) ? 'confirmed' : 'cancelled' ?>">
                                    <?= !empty($rev['is_approved']) ? 'Approved' : 'Pending' ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-row-actions">
                                    <a href="<?= BASE_URL ?>/admin/reviews.php?toggle=<?= (int)$rev['id'] ?>" class="btn-table-action" title="Toggle Visibility">
                                        <?= !empty($rev['is_approved']) ? 'Hide' : 'Approve' ?>
                                    </a>
                                    <a href="<?= BASE_URL ?>/admin/reviews.php?delete=<?= (int)$rev['id'] ?>" class="btn-table-action delete" onclick="return confirm('Delete review permanently?');" title="Delete">🗑</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center py-4">No reviews recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
