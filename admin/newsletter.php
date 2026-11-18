<?php
/**
 * MAISON ÉLAN - Admin Newsletter Subscribers
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$db = getDB();

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv' && $db) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=maison_elan_subscribers_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Email Address', 'Status', 'Subscribed At']);

    $stmt = $db->query("SELECT id, email, status, subscribed_at FROM newsletter_subscribers ORDER BY id DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && $db) {
    try {
        $db->prepare("DELETE FROM newsletter_subscribers WHERE id = ?")->execute([(int)$_GET['delete']]);
        set_flash_message('success', 'Subscriber removed.');
        redirect(BASE_URL . '/admin/newsletter.php');
    } catch (\Exception $e) {}
}

$subscribers = [];
if ($db) {
    try {
        $subscribers = $db->query("SELECT * FROM newsletter_subscribers ORDER BY id DESC")->fetchAll();
    } catch (\Exception $e) {}
}

$adminTitle = "Newsletter Subscribers";
$pageHeading = "Private Circle Guestlist (" . count($subscribers) . ")";

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-page-actions-row">
    <div></div>
    <div>
        <a href="<?= BASE_URL ?>/admin/newsletter.php?export=csv" class="btn btn-outline btn-sm">Export to CSV ↓</a>
    </div>
</div>

<div class="admin-panel-card">
    <div class="admin-table-responsive">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Subscriber Email</th>
                    <th>Status</th>
                    <th>Subscribed On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($subscribers)): ?>
                    <?php foreach ($subscribers as $sub): ?>
                        <tr>
                            <td>
                                <strong><a href="mailto:<?= e($sub['email']) ?>"><?= e($sub['email']) ?></a></strong>
                            </td>
                            <td>
                                <span class="status-pill status-confirmed"><?= e($sub['status']) ?></span>
                            </td>
                            <td><?= date('M d, Y \a\t H:i', strtotime($sub['subscribed_at'])) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/newsletter.php?delete=<?= (int)$sub['id'] ?>" class="btn-table-action delete" onclick="return confirm('Remove subscriber?');">🗑</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center py-4">No subscribers found yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
