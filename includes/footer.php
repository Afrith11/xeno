<?php
/**
 * MAISON ÉLAN - Footer Component
 * Luxury Editorial Footer & Global Modals
 */

require_once __DIR__ . '/functions.php';
?>
    </main><!-- /#main-content -->

    <!-- Quick Shopping Bag Slide-Out Drawer -->
    <div class="cart-drawer-overlay" id="cartDrawerOverlay"></div>
    <div class="cart-drawer" id="cartDrawer" aria-modal="true" role="dialog">
        <div class="cart-drawer-header">
            <div class="cart-drawer-title-wrap">
                <h3 class="cart-drawer-title">SHOPPING BAG</h3>
                <span class="cart-drawer-count" id="drawerCartCount">(0)</span>
            </div>
            <button type="button" class="cart-drawer-close" id="cartDrawerClose" aria-label="Close bag">&times;</button>
        </div>

        <div class="cart-drawer-body" id="cartDrawerBody">
            <div class="cart-drawer-empty" id="cartDrawerEmpty">
                <svg class="empty-bag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <p class="empty-title">Your shopping bag is empty</p>
                <p class="empty-subtitle">Discover handcrafted sarees, evening dresses, and festive couture.</p>
                <a href="<?= BASE_URL ?>/shop.php" class="btn btn-outline">Explore Boutique</a>
            </div>
            <div class="cart-drawer-items" id="cartDrawerItems"></div>
        </div>

        <div class="cart-drawer-footer" id="cartDrawerFooter">
            <div class="drawer-subtotal-row">
                <span class="subtotal-label">Subtotal</span>
                <span class="subtotal-val" id="drawerSubtotal"><?= format_price(0) ?></span>
            </div>
            <p class="drawer-shipping-note">Taxes & insured delivery calculated at checkout.</p>
            <div class="drawer-action-buttons">
                <a href="<?= BASE_URL ?>/cart.php" class="btn btn-outline btn-block">View Full Bag</a>
                <a href="<?= BASE_URL ?>/checkout.php" class="btn btn-primary btn-block">Proceed to Checkout</a>
            </div>
        </div>
    </div>

    <!-- Size Guide Modal -->
    <div class="modal-backdrop" id="sizeGuideModal" aria-hidden="true">
        <div class="modal-dialog size-guide-dialog">
            <button type="button" class="modal-close-btn" id="sizeGuideClose">&times;</button>
            <div class="size-guide-header">
                <span class="eyebrow-text">ATELIER MEASUREMENTS</span>
                <h3 class="modal-title">Size & Proportion Guide</h3>
                <p class="modal-subtitle">All measurements are provided in inches. For custom bespoke tailoring, please reach out to our concierge.</p>
            </div>
            <div class="size-guide-table-wrap">
                <table class="size-guide-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Bust</th>
                            <th>Waist</th>
                            <th>Hip</th>
                            <th>Blouse Chest</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>XS</strong> (34)</td><td>32 - 33"</td><td>25 - 26"</td><td>35 - 36"</td><td>32"</td></tr>
                        <tr><td><strong>S</strong> (36)</td><td>34 - 35"</td><td>27 - 28"</td><td>37 - 38"</td><td>34"</td></tr>
                        <tr><td><strong>M</strong> (38)</td><td>36 - 37"</td><td>29 - 30"</td><td>39 - 40"</td><td>36"</td></tr>
                        <tr><td><strong>L</strong> (40)</td><td>38 - 40"</td><td>31 - 33"</td><td>41 - 43"</td><td>38"</td></tr>
                        <tr><td><strong>XL</strong> (42)</td><td>41 - 43"</td><td>34 - 36"</td><td>44 - 46"</td><td>40"</td></tr>
                        <tr><td><strong>Free Size</strong></td><td colspan="4">Engineered with flexible adjustable seams & drapes accommodating sizes XS through XL.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="size-guide-footer">
                <p>Need guidance on fit? Call our personal stylist at <a href="tel:<?= STORE_PHONE ?>"><?= STORE_PHONE ?></a>.</p>
            </div>
        </div>
    </div>

    <!-- Main Footer -->
    <footer class="site-footer">
        <!-- Brand Tagline Banner -->
        <div class="footer-statement-bar">
            <div class="site-container">
                <span class="statement-eyebrow">HAUTE COUTURE • MODERN INDIAN HERITAGE</span>
                <h2 class="statement-heading">"Designed to Be Remembered."</h2>
            </div>
        </div>

        <div class="footer-columns site-container">
            <!-- Column 1: Brand & Atelier -->
            <div class="footer-col col-brand">
                <h3 class="footer-col-title brand-title">MAISON ÉLAN</h3>
                <p class="brand-bio">
                    An editorial destination for contemporary Indian silhouettes, heritage Banarasi handlooms, and architectural evening wear crafted for life’s most celebrated chapters.
                </p>
                <div class="boutique-details">
                    <p class="address-line">42 Heritage Boulevard, Chanakyapuri, New Delhi</p>
                    <p class="contact-line">Concierge: <a href="tel:<?= STORE_PHONE ?>"><?= STORE_PHONE ?></a></p>
                    <p class="contact-line">Email: <a href="mailto:<?= STORE_EMAIL ?>"><?= STORE_EMAIL ?></a></p>
                </div>
            </div>

            <!-- Column 2: Shop -->
            <div class="footer-col col-shop">
                <h4 class="footer-col-heading">SHOP</h4>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>/shop.php?filter=new">New Arrivals</a></li>
                    <li><a href="<?= BASE_URL ?>/shop.php?category=sarees">Sarees & Silks</a></li>
                    <li><a href="<?= BASE_URL ?>/shop.php?category=dresses">Designer Dresses</a></li>
                    <li><a href="<?= BASE_URL ?>/shop.php?category=kurtis">Kurtis & Sets</a></li>
                    <li><a href="<?= BASE_URL ?>/shop.php?category=occasion-wear">Occasion Wear</a></li>
                    <li><a href="<?= BASE_URL ?>/shop.php?collection=wedding">The Wedding Edit</a></li>
                    <li><a href="<?= BASE_URL ?>/shop.php?category=everyday-luxe">Everyday Luxe</a></li>
                </ul>
            </div>

            <!-- Column 3: Help & Client Services -->
            <div class="footer-col col-help">
                <h4 class="footer-col-heading">CLIENT SERVICES</h4>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>/contact.php">Contact & Concierge</a></li>
                    <li><a href="<?= BASE_URL ?>/orders.php">Track Your Order</a></li>
                    <li><a href="<?= BASE_URL ?>/shipping-policy.php">Complimentary Shipping</a></li>
                    <li><a href="<?= BASE_URL ?>/return-policy.php">Returns & Exchanges</a></li>
                    <li><a href="javascript:void(0)" id="openSizeGuideLink">Size & Fit Guide</a></li>
                    <li><a href="<?= BASE_URL ?>/about.php#craftsmanship">Fabric & Care Guide</a></li>
                </ul>
            </div>

            <!-- Column 4: Private Circle & Social -->
            <div class="footer-col col-connect">
                <h4 class="footer-col-heading">PRIVATE CIRCLE</h4>
                <p class="newsletter-desc">
                    Receive invitations to private collection debuts, bespoke trunk shows, and editorial previews.
                </p>
                
                <form class="footer-newsletter-form" id="footerNewsletterForm">
                    <div class="newsletter-input-group">
                        <input type="email" name="email" class="newsletter-input" placeholder="Your email address" required>
                        <button type="submit" class="newsletter-btn" aria-label="Join Private Circle">→</button>
                    </div>
                    <span class="newsletter-status" id="footerNewsletterStatus"></span>
                </form>

                <div class="footer-social-wrap">
                    <span class="social-label">Follow The Élan:</span>
                    <div class="social-links">
                        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" aria-label="Instagram">Instagram</a>
                        <span class="sep">•</span>
                        <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener" aria-label="Facebook">Facebook</a>
                        <span class="sep">•</span>
                        <a href="<?= SOCIAL_PINTEREST ?>" target="_blank" rel="noopener" aria-label="Pinterest">Pinterest</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom-bar">
            <div class="footer-bottom-inner site-container">
                <p class="copyright-text">
                    &copy; 2026 MAISON ÉLAN. All Rights Reserved. Crafted for Haute Couture.
                </p>
                <div class="legal-links">
                    <a href="<?= BASE_URL ?>/privacy-policy.php">Privacy Policy</a>
                    <a href="<?= BASE_URL ?>/terms.php">Terms & Conditions</a>
                    <a href="<?= BASE_URL ?>/shipping-policy.php">Shipping Policy</a>
                    <a href="<?= BASE_URL ?>/return-policy.php">Refund & Exchange Policy</a>
                    <a href="<?= BASE_URL ?>/admin/login.php" class="admin-link">Atelier Portal</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= BASE_URL ?>/assets/js/main.js?v=1.0"></script>
    <script src="<?= BASE_URL ?>/assets/js/cart.js?v=1.0"></script>
</body>
</html>
