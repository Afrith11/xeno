<?php
/**
 * MAISON ÉLAN - Shipping Policy
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "Shipping & White-Glove Delivery — Maison Élan Haute Couture";
$pageDescription = "Read about our complimentary insured courier service, white-glove packaging, and transit timelines.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="policy-page-container site-container">
    <div class="policy-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">Shipping Policy</span>
        </nav>
        <span class="policy-eyebrow">CLIENT SERVICES</span>
        <h1 class="policy-title">SHIPPING & WHITE-GLOVE DELIVERY</h1>
        <p class="policy-sub">Our commitment to ensuring every bespoke creation arrives in pristine atelier condition.</p>
    </div>

    <div class="policy-content-body">
        <div class="policy-section-block">
            <h2>1. Complimentary Insured Delivery</h2>
            <p>Maison Élan provides complimentary fully-insured white-glove shipping on all domestic orders exceeding <?= format_price(FREE_SHIPPING_THRESHOLD) ?> across all pin-codes in India. For orders below this threshold, a standardized nominal handling fee of <?= format_price(FLAT_SHIPPING_RATE) ?> is applied at checkout.</p>
        </div>

        <div class="policy-section-block">
            <h2>2. Dispatch & Atelier Preparation</h2>
            <p>Ready-to-wear silhouettes and catalog ensembles are dispatched from our New Delhi atelier within 24 to 48 business hours. Each item undergoes a rigorous 14-point quality inspection by master karigars before packaging.</p>
            <p>Custom made-to-measure couture, bespoke bridal lehengas, and tailored blouse orders require an atelier craft timeline of 7 to 14 business days prior to dispatch.</p>
        </div>

        <div class="policy-section-block">
            <h2>3. Transit Timelines</h2>
            <ul>
                <li><strong>Delhi NCR & Metro Cities:</strong> 2 to 3 business days from dispatch.</li>
                <li><strong>Tier 1 & Tier 2 Cities:</strong> 3 to 5 business days from dispatch.</li>
                <li><strong>Remote Locations:</strong> 5 to 7 business days from dispatch.</li>
            </ul>
        </div>

        <div class="policy-section-block">
            <h2>4. Signature Presentation Packaging</h2>
            <p>Every Maison Élan garment is enveloped in breathable, acid-free archival cotton covers, wrapped in tissue paper with gold leaf accents, and enclosed in our signature magnetic-closure keepsake boxes designed to preserve delicate hand embroideries for years to come.</p>
        </div>

        <div class="policy-section-block">
            <h2>5. Real-Time Tracking & Proof of Delivery</h2>
            <p>Upon courier pickup, an automated dispatch notification containing your direct tracking AWB link is transmitted via SMS and email. All deliveries require an authorized recipient signature upon receipt.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
