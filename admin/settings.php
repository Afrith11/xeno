<?php
/**
 * MAISON ÉLAN - Admin Settings
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_admin();

$adminTitle = "Boutique Settings";
$pageHeading = "Atelier & Gateway Configuration";

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $fields = [
            'store_name' => trim($_POST['store_name'] ?? 'MAISON ÉLAN'),
            'store_tagline' => trim($_POST['store_tagline'] ?? 'Designed to Be Remembered.'),
            'store_email' => trim($_POST['store_email'] ?? 'concierge@maisonelan.com'),
            'store_phone' => trim($_POST['store_phone'] ?? '+91 98765 43210'),
            'store_address' => trim($_POST['store_address'] ?? ''),
            'free_shipping_threshold' => trim($_POST['free_shipping_threshold'] ?? '5000'),
            'flat_shipping_rate' => trim($_POST['flat_shipping_rate'] ?? '250'),
            'razorpay_key_id' => trim($_POST['razorpay_key_id'] ?? ''),
            'razorpay_key_secret' => trim($_POST['razorpay_key_secret'] ?? '')
        ];

        if ($db) {
            try {
                $upsert = $db->prepare("INSERT INTO settings (setting_key, setting_value, updated_at) 
                                        VALUES (?, ?, NOW()) 
                                        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()");
                foreach ($fields as $key => $val) {
                    $upsert->execute([$key, $val]);
                }
                set_flash_message('success', 'Boutique settings saved successfully.');
            } catch (\Exception $e) {
                set_flash_message('error', 'Error saving settings: ' . $e->getMessage());
            }
        }
        redirect(BASE_URL . '/admin/settings.php');
    }
}

// Load settings from DB
$settings = [
    'store_name' => SITE_NAME,
    'store_tagline' => SITE_TAGLINE,
    'store_email' => STORE_EMAIL,
    'store_phone' => STORE_PHONE,
    'store_address' => STORE_ADDRESS,
    'free_shipping_threshold' => FREE_SHIPPING_THRESHOLD,
    'flat_shipping_rate' => FLAT_SHIPPING_RATE,
    'razorpay_key_id' => RAZORPAY_KEY_ID,
    'razorpay_key_secret' => RAZORPAY_KEY_SECRET
];

if ($db) {
    try {
        $rows = $db->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }
    } catch (\Exception $e) {}
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-panel-card" style="max-width: 800px;">
    <form action="<?= BASE_URL ?>/admin/settings.php" method="POST" class="admin-form">
        <?= csrf_field() ?>

        <div class="settings-section-divider">
            <h3 class="panel-title">1. Boutique Brand Identity</h3>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="storeName" class="form-label">Boutique Name</label>
                <input type="text" id="storeName" name="store_name" class="form-control" value="<?= e($settings['store_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="storeTagline" class="form-label">Brand Tagline</label>
                <input type="text" id="storeTagline" name="store_tagline" class="form-control" value="<?= e($settings['store_tagline']) ?>" required>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="storeEmail" class="form-label">Concierge Email</label>
                <input type="email" id="storeEmail" name="store_email" class="form-control" value="<?= e($settings['store_email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="storePhone" class="form-label">Concierge Phone / WhatsApp</label>
                <input type="text" id="storePhone" name="store_phone" class="form-control" value="<?= e($settings['store_phone']) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="storeAddress" class="form-label">Flagship Salon Address</label>
            <textarea id="storeAddress" name="store_address" class="form-control" rows="2"><?= e($settings['store_address']) ?></textarea>
        </div>

        <div class="settings-section-divider" style="margin-top: 32px;">
            <h3 class="panel-title">2. Shipping & Commerce Thresholds</h3>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="freeShipping" class="form-label">Free Shipping Minimum Bag Value (₹)</label>
                <input type="number" id="freeShipping" name="free_shipping_threshold" class="form-control" value="<?= e($settings['free_shipping_threshold']) ?>" required>
            </div>
            <div class="form-group">
                <label for="flatRate" class="form-label">Standard Flat Shipping Fee (₹)</label>
                <input type="number" id="flatRate" name="flat_shipping_rate" class="form-control" value="<?= e($settings['flat_shipping_rate']) ?>" required>
            </div>
        </div>

        <div class="settings-section-divider" style="margin-top: 32px;">
            <h3 class="panel-title">3. Razorpay Payment Gateway Integration</h3>
            <p class="panel-sub">Obtained from your Razorpay Dashboard under Settings &rarr; API Keys.</p>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="rzpKey" class="form-label">Razorpay Key ID</label>
                <input type="text" id="rzpKey" name="razorpay_key_id" class="form-control" placeholder="rzp_live_..." value="<?= e($settings['razorpay_key_id']) ?>">
            </div>
            <div class="form-group">
                <label for="rzpSecret" class="form-label">Razorpay Key Secret</label>
                <input type="password" id="rzpSecret" name="razorpay_key_secret" class="form-control" placeholder="••••••••" value="<?= e($settings['razorpay_key_secret']) ?>">
            </div>
        </div>

        <div class="form-actions" style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary btn-lg">SAVE ATELIER CONFIGURATION</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
