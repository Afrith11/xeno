<?php
/**
 * MAISON ÉLAN - Concierge & Contact
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security session. Please retry.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $inquiryType = trim($_POST['inquiry_type'] ?? 'General Inquiry');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            $error = 'Please fill in your name, email address, and message.';
        } else {
            // Success response
            $success = "Thank you, {$name}. Your enquiry has been routed to our Head of Client Relations. We will respond within 12 business hours.";
        }
    }
}

$pageTitle = "Contact & Concierge — Maison Élan Haute Couture";
$pageDescription = "Connect with the Maison Élan concierge team for bespoke fittings, bridal consultations, and order assistance.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="contact-page-container site-container">
    <div class="account-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">Concierge & Contact</span>
        </nav>
        <h1 class="account-title">ATELIER CONCIERGE</h1>
        <p class="account-sub">Dedicated assistance for bespoke styling, private salon visits, and order tracking.</p>
    </div>

    <div class="contact-grid-layout">
        <!-- Contact Information Column -->
        <div class="contact-info-col">
            <div class="contact-card-box">
                <span class="contact-badge">FLAGSHIP SALON</span>
                <h3 class="contact-city-title">Chanakyapuri, New Delhi</h3>
                
                <div class="contact-detail-group">
                    <span class="detail-label">Atelier Address</span>
                    <p class="detail-val"><?= STORE_ADDRESS ?></p>
                </div>

                <div class="contact-detail-group">
                    <span class="detail-label">Client Concierge</span>
                    <p class="detail-val"><a href="tel:<?= STORE_PHONE ?>"><?= STORE_PHONE ?></a></p>
                    <p class="detail-val"><a href="mailto:<?= STORE_EMAIL ?>"><?= STORE_EMAIL ?></a></p>
                </div>

                <div class="contact-detail-group">
                    <span class="detail-label">Salon Hours</span>
                    <p class="detail-val"><?= STORE_HOURS ?></p>
                    <p class="detail-note">Sundays by private appointment only.</p>
                </div>

                <div class="contact-whatsapp-banner">
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', STORE_WHATSAPP) ?>?text=Hello%20Maison%20%C3%89lan%2C%20I%20would%20like%20to%20inquire%20about%20a%20silhouette." target="_blank" rel="noopener" class="btn btn-outline btn-block">
                        Chat on WhatsApp &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Contact Form Column -->
        <div class="contact-form-col">
            <div class="contact-card-box">
                <h3 class="contact-form-title">Send An Inquiry</h3>
                <p class="contact-form-sub">Kindly detail your requirements and our concierge will reach out promptly.</p>

                <?php if (!empty($success)): ?>
                    <div class="auth-alert success"><?= e($success) ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="auth-alert error"><?= e($error) ?></div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/contact.php" method="POST" class="contact-form">
                    <?= csrf_field() ?>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="contactName" class="form-label">Full Name *</label>
                            <input type="text" id="contactName" name="name" class="form-control" value="<?= e($_POST['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contactEmail" class="form-label">Email Address *</label>
                            <input type="email" id="contactEmail" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="contactPhone" class="form-label">Mobile Number</label>
                            <input type="tel" id="contactPhone" name="phone" class="form-control" placeholder="+91 98765 43210" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="contactType" class="form-label">Nature of Inquiry</label>
                            <select id="contactType" name="inquiry_type" class="form-control">
                                <option value="Bespoke Couture Consultation">Bespoke Couture Consultation</option>
                                <option value="Bridal Trousseau Inquiry">Bridal Trousseau Inquiry</option>
                                <option value="Order & Delivery Tracking">Order & Delivery Tracking</option>
                                <option value="Size & Fit Styling Guidance">Size & Fit Styling Guidance</option>
                                <option value="Press & Media Relations">Press & Media Relations</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contactMessage" class="form-label">Message & Notes *</label>
                        <textarea id="contactMessage" name="message" class="form-control" rows="5" placeholder="Please specify outfit interests, occasion dates, or fit inquiries..." required><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">DISPATCH INQUIRY</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
