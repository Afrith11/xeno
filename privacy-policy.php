<?php
/**
 * MAISON ÉLAN - Privacy Policy
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "Privacy Policy — Maison Élan Haute Couture";
$pageDescription = "Maison Élan client privacy guidelines and data protection commitments.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="policy-page-container site-container">
    <div class="policy-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current">Privacy Policy</span>
        </nav>
        <span class="policy-eyebrow">LEGAL & COMPLIANCE</span>
        <h1 class="policy-title">PRIVACY POLICY</h1>
        <p class="policy-sub">Protecting client information with the highest security standards.</p>
    </div>

    <div class="policy-content-body">
        <div class="policy-section-block">
            <h2>1. Commitment to Client Confidentiality</h2>
            <p>Maison Élan ("we", "our", "us") values the trust placed in our atelier by our discerning patrons. This Privacy Policy outlines our strict protocols regarding the collection, protection, and utilization of personal information.</p>
        </div>

        <div class="policy-section-block">
            <h2>2. Information We Collect</h2>
            <p>We collect essential information required to fulfill boutique orders, coordinate bespoke appointments, and provide seamless customer service, including: your name, billing and shipping addresses, telephone number, email address, and styling measurement preferences.</p>
        </div>

        <div class="policy-section-block">
            <h2>3. Secure Payment Processing</h2>
            <p>We do not store complete credit card or debit card numbers on our shared servers. All digital transactions are processed through RBI-authorized, PCI-DSS Level 1 compliant payment gateways (such as Razorpay) utilizing 256-bit SSL encryption.</p>
        </div>

        <div class="policy-section-block">
            <h2>4. Non-Disclosure & Data Protection</h2>
            <p>We never sell, rent, or trade your personal information with external marketers. Client data is strictly used for order processing, logistics coordination, and optional invitations to private atelier previews.</p>
        </div>

        <div class="policy-section-block">
            <h2>5. Contact Our Data Protection Officer</h2>
            <p>Should you wish to inspect, modify, or request deletion of your client data, please communicate with our concierge at <a href="mailto:<?= STORE_EMAIL ?>"><?= STORE_EMAIL ?></a>.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
