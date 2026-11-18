<?php
/**
 * MAISON ÉLAN - Terms of Service
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "Terms of Service — Maison Élan Haute Couture";
$pageDescription = "Terms and conditions governing the use of Maison Élan digital boutique and boutique orders.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="policy-page-container site-container">
    <div class="policy-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">Terms of Service</span>
        </nav>
        <span class="policy-eyebrow">LEGAL & COMPLIANCE</span>
        <h1 class="policy-title">TERMS OF SERVICE</h1>
        <p class="policy-sub">Standards and agreements governing your interaction with Maison Élan.</p>
    </div>

    <div class="policy-content-body">
        <div class="policy-section-block">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing the digital salon of Maison Élan or purchasing haute couture silhouettes from our boutique, you acknowledge and agree to abide by these Terms of Service.</p>
        </div>

        <div class="policy-section-block">
            <h2>2. Handcrafted Variations & Handloom Disclaimers</h2>
            <p>Our pieces are crafted from pure handloom silks, raw organzas, and feature hand-applied zardozi embroidery. Subtle irregularities in weave, slubs in handspun silk, and minor dye variations are inherent hallmarks of authentic artisan craft and should not be misconstrued as defects.</p>
        </div>

        <div class="policy-section-block">
            <h2>3. Pricing & Taxes</h2>
            <p>All prices displayed on Maison Élan are quoted in Indian Rupees (INR) and are inclusive of Goods & Services Tax (GST). We reserve the right to modify prices for bespoke commissions or adjust rates in accordance with raw material precious metal fluctuations.</p>
        </div>

        <div class="policy-section-block">
            <h2>4. Intellectual Property</h2>
            <p>All editorial photography, bespoke silhouette designs, brand marks, and typography displayed on this platform are the exclusive intellectual property of Maison Élan Haute Couture and are protected under Indian and international copyright law.</p>
        </div>

        <div class="policy-section-block">
            <h2>5. Governing Jurisdiction</h2>
            <p>Any disputes arising under these Terms shall be subject to the exclusive jurisdiction of the competent courts of New Delhi, India.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
