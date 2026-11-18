<?php
/**
 * MAISON ÉLAN - About The Atelier
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "About The Atelier — Maison Élan Haute Couture";
$pageDescription = "Discover the story of Maison Élan. Born from a reverence for heritage Indian textiles, architectural drape, and slow couture.";

require_once __DIR__ . '/includes/header.php';
?>

<!-- About Hero -->
<section class="editorial-about-hero">
    <div class="site-container">
        <span class="editorial-kicker">HAUTE COUTURE • NEW DELHI</span>
        <h1 class="about-hero-title">THE ARCHITECTURE OF<br><em>MEMORABLE POISE</em></h1>
        <div class="about-emblem-divider">❖</div>
    </div>
</section>

<!-- About Story Section -->
<section class="about-story-section site-container">
    <div class="about-story-grid">
        <div class="story-img-col">
            <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1200&q=80" alt="Maison Élan Atelier Craftsmanship" class="story-img">
            <span class="story-caption">Master artisans at work at our Chanakyapuri atelier.</span>
        </div>
        <div class="story-text-col">
            <span class="section-eyebrow">OUR GENESIS</span>
            <h2 class="story-headline">"Fashion is not what you wear. It is how you are remembered."</h2>
            <p class="story-lead">
                Founded with an unwavering commitment to elevate traditional Indian craftsmanship into modern architectural expressions, Maison Élan bridges the sacred looms of Varanasi and Chanderi with the sleek geometry of international evening wear.
            </p>
            <p class="story-body">
                We reject seasonal disposability in favor of heirloom longevity. Each drape is engineered to move like liquid poetry, respecting the natural weight of handspun mulberry silks and sheer organzas while providing modern ease for celebratory evenings.
            </p>
        </div>
    </div>
</section>

<!-- Craftsmanship Pillars -->
<section class="craft-pillars-section" id="craftsmanship">
    <div class="site-container">
        <div class="section-editorial-header">
            <span class="section-eyebrow">THE FOUR PILLARS</span>
            <h2 class="section-title">Atelier Standards</h2>
        </div>

        <div class="pillars-grid">
            <div class="pillar-card">
                <span class="pillar-num">01</span>
                <h3 class="pillar-title">Certified Handloom Silks</h3>
                <p>Every meter of Banarasi katan and raw silk is directly commissioned from multigenerational weaver families, guaranteeing authentic zari and hand-knotting.</p>
            </div>
            <div class="pillar-card">
                <span class="pillar-num">02</span>
                <h3 class="pillar-title">Zardozi & Marodi Needlecraft</h3>
                <p>Our embellishments utilize dull antique gold bullion, pearls, and micro bugle beads stitched onto stabilized silks to ensure featherweight comfort.</p>
            </div>
            <div class="pillar-card">
                <span class="pillar-num">03</span>
                <h3 class="pillar-title">Sculpted Tailoring</h3>
                <p>From pre-pleated drape sarees that slip on in forty seconds to boned corset evening gowns, our patterns are drafted with millimeter precision.</p>
            </div>
            <div class="pillar-card">
                <span class="pillar-num">04</span>
                <h3 class="pillar-title">Slow Couture Ethos</h3>
                <p>We produce in strictly limited runs of 10–25 pieces per silhouette. Once an atelier run is spoken for, it is permanently retired to our archive.</p>
            </div>
        </div>
    </div>
</section>

<!-- Atelier Appointment CTA -->
<section class="appointment-cta-section site-container">
    <div class="appointment-card">
        <span class="section-eyebrow">BESPOKE SERVICES</span>
        <h2 class="appointment-title">Private Atelier Appointments</h2>
        <p class="appointment-desc">
            Visit our flagship salon in Chanakyapuri, New Delhi for customized bridal trousseau consultations, made-to-measure fittings, and private archive viewings.
        </p>
        <div class="appointment-contact-row">
            <a href="<?= BASE_URL ?>/contact.php" class="btn btn-primary btn-lg">Request an Appointment</a>
            <a href="tel:<?= STORE_PHONE ?>" class="btn btn-outline btn-lg">Call Concierge: <?= STORE_PHONE ?></a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
