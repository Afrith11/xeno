<?php
/**
 * MAISON ÉLAN - Return & Exchange Policy
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "Returns & Exchanges — Maison Élan Haute Couture";
$pageDescription = "Information regarding our seamless 7-day return and size exchange policy for designer garments.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="policy-page-container site-container">
    <div class="policy-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">Returns & Exchanges</span>
        </nav>
        <span class="policy-eyebrow">CLIENT SERVICES</span>
        <h1 class="policy-title">RETURNS & EXCHANGES</h1>
        <p class="policy-sub">Our seamless 7-day policy for flawless client satisfaction.</p>
    </div>

    <div class="policy-content-body">
        <div class="policy-section-block">
            <h2>1. The 7-Day Boutique Return Window</h2>
            <p>At Maison Élan, client satisfaction is our highest priority. If a ready-to-wear silhouette does not meet your expectations in fit or aesthetic, you may request a return or size exchange within <strong>7 calendar days</strong> of receiving your delivery.</p>
        </div>

        <div class="policy-section-block">
            <h2>2. Condition of Garments</h2>
            <p>To qualify for a refund or exchange:</p>
            <ul>
                <li>The garment must remain unworn, unaltered, and unwashed.</li>
                <li>The original black & gold security tag must remain unbroken and securely fastened to the garment.</li>
                <li>The piece must be packed within its original keepsake box and dust bag.</li>
            </ul>
        </div>

        <div class="policy-section-block">
            <h2>3. Complimentary Size Exchanges</h2>
            <p>If you require an alternate size, we arrange a complimentary reverse courier pickup from your doorstep and prioritize dispatch of the replacement piece at no additional expense.</p>
        </div>

        <div class="policy-section-block">
            <h2>4. Bespoke & Made-to-Measure Exceptions</h2>
            <p>Due to the hyper-individualized nature of bespoke wedding lehengas and personalized tailoring, these pieces are non-returnable. However, we provide complimentary lifetime alteration adjustments at our New Delhi flagship salon.</p>
        </div>

        <div class="policy-section-block">
            <h2>5. Refund Process</h2>
            <p>Once your returned silhouette is inspected by our atelier team, your refund is processed within 48 business hours to your original mode of payment (or via direct NEFT bank transfer for Cash on Delivery orders).</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
