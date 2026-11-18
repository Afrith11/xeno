# MAISON ÉLAN — Haute Couture Designer Fashion Boutique
> *"Designed to Be Remembered."*

A luxury, editorial-driven e-commerce platform crafted for **MAISON ÉLAN**, a high-end designer fashion boutique featuring bespoke sarees, architectural gowns, Chanderi kurti sets, and curated wedding trousseau collections.

Engineered specifically for **Hostinger shared hosting** (PHP 8+, MySQL, PDO with prepared statements everywhere, vanilla JavaScript, no Node/npm, no build step, no separate server).

---

## Table of Contents
1. [Brand Concept & Design Direction](#1-brand-concept--design-direction)
2. [Folder & File Architecture](#2-folder--file-architecture)
3. [Hostinger Deployment Guide (Step-by-Step)](#3-hostinger-deployment-guide-step-by-step)
4. [Database Setup & 1-Click Installer](#4-database-setup--1-click-installer)
5. [Configuration Instructions](#5-configuration-instructions)
6. [Atelier Admin Console & Credentials](#6-atelier-admin-console--credentials)
7. [Razorpay Payment Gateway Integration](#7-razorpay-payment-gateway-integration)
8. [E-commerce Features Checklist](#8-e-commerce-features-checklist)
9. [Security & Optimization Measures](#9-security--optimization-measures)

---

## 1. Brand Concept & Design Direction

- **Brand Name:** MAISON ÉLAN
- **Tagline:** *"Designed to Be Remembered."*
- **Aesthetic:** Editorial fashion magazine meets modern Indian designer salon. High whitespace, hairline borders, fine typography, and authentic handloom imagery.
- **Color System:**
  - Primary Background: Warm Ivory (`#FAF7F2` / `#FDFBF7`)
  - Primary Text: Deep Charcoal (`#161616`)
  - Accents: Antique Gold (`#C5A880`, `#A68B63`)
  - Supporting Tones: Soft Beige (`#EFE9E0`), Warm Taupe (`#A89F91`), Muted Brown (`#6E5D4F`)
- **Typography:**
  - Headings: *Cormorant Garamond* & *Playfair Display* (Google Fonts CDN)
  - Body: *Inter* (Google Fonts CDN)
- **Currency:** Indian Rupee (`₹` / `INR`) with standard Indian numbering formatting.

---

## 2. Folder & File Architecture

```
/
├── index.php                 # Visually stunning 11-section editorial homepage
├── shop.php                  # Product catalog with filters (category, size, color, price) & sorting
├── product.php               # Product detail page: gallery zoom, size guide, accordions, reviews, JSON-LD
├── cart.php                  # Interactive shopping bag with coupon redemption & free delivery progress
├── checkout.php              # Two-column checkout (COD & Razorpay support with CSRF protection)
├── order-confirmation.php    # Post-order success screen with printable receipt & timeline
├── login.php                 # Customer authentication with session regeneration
├── register.php              # Customer registration with bcrypt hashing
├── logout.php                # Customer session termination
├── account.php               # Customer salon profile, delivery addresses & preferences
├── orders.php                # Customer order history & guest shipment tracking lookup
├── order-detail.php          # Detailed order breakdown with live fulfillment tracker
├── wishlist.php              # Saved silhouettes with 1-click move to bag
├── about.php                 # Brand genesis, handloom pillars, and atelier salon appointments
├── contact.php               # Atelier concierge inquiry form & Delhi flagship details
├── lookbook.php              # Full masonry lookbook with interactive image lightbox
├── shipping-policy.php       # Complimentary white-glove shipping terms
├── return-policy.php         # 7-day boutique returns & size exchange policy
├── privacy-policy.php        # Client confidentiality & data protection
├── terms.php                 # Terms of service & handloom disclaimers
├── database.sql              # Complete MySQL DDL schema + 12 realistic designer pieces seed data
├── install.php               # 1-Click web installer & hosting diagnostics tool
├── robots.txt                # Search engine directives (blocks admin/api)
├── sitemap.xml               # Complete XML sitemap for SEO
├── .htaccess                 # Apache rewrite rules for clean URLs, Gzip, and browser caching
│
├── config/
│   ├── config.php            # Environment settings, constants, DB credentials, Razorpay keys
│   └── database.php          # Singleton PDO connection with UTF-8 (utf8mb4) & exception handling
│
├── includes/
│   ├── header.php            # Sticky header, announcement bar, mobile drawer, search overlay, quickview
│   ├── footer.php            # 4-column editorial footer, legal links, quick bag drawer, size guide
│   ├── functions.php         # Helper functions: format_price(), csrf, slugify, rating stars, queries
│   ├── auth.php              # Customer login, register, password_verify() and session security
│   ├── cart-functions.php    # Add, update, remove, coupon discounts, shipping threshold logic
│   └── product-card.php      # Reusable product card with dual-image hover, quickview, and badges
│
├── api/
│   ├── cart.php              # AJAX endpoint for cart operations (add, update, remove, coupon)
│   ├── wishlist.php          # AJAX endpoint for wishlist toggling
│   ├── newsletter.php        # AJAX endpoint for newsletter subscriptions
│   ├── quickview.php         # AJAX endpoint returning HTML modal for instant product preview
│   └── search.php            # AJAX endpoint for real-time live search preview
│
├── admin/
│   ├── index.php             # Admin dashboard: metrics (orders, sales, pending, patrons, products)
│   ├── login.php             # Admin authentication with bcrypt verification
│   ├── logout.php            # Admin session logout
│   ├── products.php          # Silhouette management with search, filter, stock badges
│   ├── product-add.php       # Add silhouette with file upload or URL, sizes, colors, flags
│   ├── product-edit.php      # Edit silhouette details and manage photography
│   ├── product-delete.php    # Safely retire silhouette from catalog
│   ├── categories.php        # Manage categories and view product counts
│   ├── orders.php            # Orders management with status filters
│   ├── order-view.php        # Detailed order review, status updates, tracking numbers, print invoice
│   ├── customers.php         # Registered patrons list with order counts and spent totals
│   ├── coupons.php           # Issue & manage promo codes (percentage or fixed discount)
│   ├── reviews.php           # Customer reviews moderation (approve / hide / delete)
│   ├── newsletter.php        # Subscribers list with 1-click CSV export
│   ├── settings.php          # Store information, shipping rules, and Razorpay API keys
│   ├── includes/
│   │   ├── admin-auth.php    # Admin session verification
│   │   ├── admin-header.php  # Dark sidebar navigation layout
│   │   └── admin-footer.php  # Admin scripts
│   └── assets/
│       ├── css/admin.css     # Clean modern admin console styling
│       └── js/admin.js       # Admin UI behaviors and auto-SKU generation
│
└── assets/
    ├── css/
    │   ├── main.css          # Primary luxury editorial styles (warm ivory, Cormorant Garamond, gold)
    │   └── responsive.css    # Comprehensive mobile & tablet optimizations
    ├── js/
    │   ├── main.js           # Header scroll, mobile drawer, search modal, quickview, accordions, lightbox
    │   └── cart.js           # AJAX cart additions, bag drawer, quantity steppers, toast alerts
    ├── images/               # Local brand assets
    └── uploads/
        └── products/         # Directory for uploaded product photography
```

---

## 3. Hostinger Deployment Guide (Step-by-Step)

Deploying MAISON ÉLAN to basic Hostinger shared hosting takes less than 5 minutes.

### Step 1: Upload Files to `public_html`
1. Log into your **Hostinger hPanel** (`hpanel.hostinger.com`).
2. Navigate to **Websites** &rarr; click **Manage** on your domain.
3. Open **File Manager** (or connect via FTP using FileZilla).
4. Enter the `public_html/` root folder.
5. Upload all the files and folders from this project directly into `public_html/`.
   - Ensure `.htaccess` is uploaded (enable "Show hidden files" in File Manager if needed).

### Step 2: Set File Permissions
Ensure the upload directory has write permissions so the admin panel can upload product photos:
- `assets/uploads/products/` &rarr; `755` (or `775`)

---

## 4. Database Setup & 1-Click Installer

You can set up the database in **either of two ways**:

### Method A: Using the Built-In 1-Click Web Installer (Recommended)
1. In Hostinger hPanel, go to **Databases** &rarr; **Management**.
2. Create a new MySQL Database (e.g. `u123456789_elan`) and MySQL User with a password.
3. Open your browser and visit:
   ```
   https://yourdomain.com/install.php
   ```
4. Enter your MySQL database name, username, and password.
5. Click **"Execute 1-Click Installation"**.
   - The installer checks PHP 8+ and PDO extensions.
   - Automatically executes `database.sql` creating all 13 tables and seeding the 12 designer products.
   - Automatically updates `config/config.php` with your credentials!

### Method B: Manual phpMyAdmin Import
1. In Hostinger hPanel, open **phpMyAdmin** for your database.
2. Select your database from the left sidebar.
3. Click the **Import** tab at the top.
4. Choose the `database.sql` file from your computer.
5. Click **Import** (or **Go**) at the bottom.
6. Open `config/config.php` and enter your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_database_user');
   define('DB_PASS', 'your_database_password');
   ```

---

## 5. Configuration Instructions

All core settings are housed in [`config/config.php`](file:///c:/Afrith/XENO/SAMPLE%201/config/config.php):

```php
// Brand Information
define('SITE_NAME', 'MAISON ÉLAN');
define('SITE_TAGLINE', 'Designed to Be Remembered.');

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'maison_elan');
define('DB_USER', 'root');
define('DB_PASS', '');

// E-commerce Pricing Rules
define('CURRENCY_SYMBOL', '₹');
define('CURRENCY_CODE', 'INR');
define('FREE_SHIPPING_THRESHOLD', 5000); // Complimentary delivery above ₹5,000
define('FLAT_SHIPPING_RATE', 250);       // Standard delivery fee for orders below ₹5,000

// Razorpay Keys (Set in config or via Admin Settings)
define('RAZORPAY_KEY_ID', 'rzp_test_placeholder_key');
define('RAZORPAY_KEY_SECRET', 'placeholder_secret_key');
```

> [!TIP]
> The site features dynamic base URL detection: whether you install it in the root `public_html` or in a subfolder like `public_html/shop/`, all asset URLs, API routes, and page links resolve automatically without hardcoding paths!

---

## 6. Atelier Admin Console & Credentials

The secure admin panel is accessible at `/admin/`.

- **Admin Login URL:** `https://yourdomain.com/admin/login.php`
- **Default Username:** `admin`
- **Default Password:** `Admin@12345`

### Pre-Configured Test Customer Account:
- **Customer Email:** `aanya@example.com`
- **Password:** `Customer@123`

### Pre-Configured Promotional Privilege Codes:
- `ELAN10` &rarr; 10% Off (Minimum bag value ₹3,000, Max discount ₹2,000)
- `WELCOME500` &rarr; Flat ₹500 Off (Minimum bag value ₹4,000)
- `ROYAL15` &rarr; 15% Off (Minimum bag value ₹8,000, Max discount ₹3,500)

---

## 7. Razorpay Payment Gateway Integration

The checkout system supports both **Cash on Delivery (COD)** and **Online Payments via Razorpay**.

### How Razorpay is Integrated:
1. **Configuration:** The API Key ID and Key Secret can be set in `config/config.php` or modified directly in the **Admin Panel &rarr; Settings**.
2. **Server-Side Verification:**
   - In `checkout.php`, orders placed with `payment_method = 'razorpay'` have their `payment_status` initialized to `Pending`.
   - The Razorpay checkout script can be initialized with the order amount in paise (`$grand_total * 100`) and the `RAZORPAY_KEY_ID`.
   - When Razorpay returns `razorpay_payment_id` and `razorpay_signature`, signature verification must be executed using HMAC SHA256 against `RAZORPAY_KEY_SECRET` before marking the order as `Paid`.
   - **Never trust client-only success.** Server verification is prepared in `checkout.php` and `api/order.php`.

---

## 8. E-commerce Features Checklist

### Homepage (11 Distinct Editorial Sections):
1. **Full Screen Hero (86vh):** High-fashion Indian model photography, tagline *"NEW SEASON COLLECTION 2026"*, *"THE ART OF DRESSING"*, subtle zoom animation, *"SHOP NEW ARRIVALS"* & *"EXPLORE COLLECTION"* CTAs.
2. **Brand Statement:** *"Fashion is not what you wear. It is how you are remembered."* with ample luxury whitespace and atelier emblem.
3. **Shop by Category:** 4 editorial cards (*Sarees*, *Dresses*, *Occasion Wear*, *Everyday Luxe*) with hover zoom and upward title reveal.
4. **New Arrivals (8 Products):** Interactive grid featuring the 8 required boutique items with alternate hover photography, wishlist heart, quick view, and add to bag.
5. **Featured Collection (Split Asymmetric Editorial):** *"THE WEDDING EDIT 2026"* with bridal silk photography and made-to-measure guarantees.
6. **Bestsellers Carousel:** Smooth horizontal scrolling product carousel with navigation arrows.
7. **Lookbook (Masonry + Lightbox):** 6 curated looks (*Look 01* through *Look 06*) that open high-resolution photographs in an overlay lightbox modal.
8. **Why Maison Élan:** 4 minimal line-icon features (*Handpicked Design*, *Quality Fabrics*, *Crafted with Detail*, *Delivered with Care*).
9. **Customer Reviews:** Verified client testimonials with name and city.
10. **Instagram Salon:** *"FOLLOW THE ÉLAN"* 6-tile grid linked to `@maisonelan`.
11. **Newsletter Subscription:** *"BE THE FIRST TO KNOW"* AJAX subscription to the private circle.

### Product Catalog (`shop.php`):
- Filter by Category, Available Sizes (`XS`, `S`, `M`, `L`, `XL`, `Free Size`), Color Palette, and Custom Price Range.
- Sort by Newest, Price Low to High, Price High to Low, Most Coveted, and Highest Rated.
- Mobile filter drawer sliding smoothly from the side with active filters.
- Pagination controls and results count.

### Product Detail Page (`product.php`):
- Thumbnail gallery with interactive stage zoom.
- Size selector with visual button swatches + **Size Guide Modal** with measurement table (Bust, Waist, Hip, Blouse Chest).
- Color selector and quantity stepper.
- **ADD TO SHOPPING BAG** and **BUY IT NOW** (instant checkout redirect).
- Accordions for *Product Details*, *Fabric & Care*, *Shipping*, and *Returns*.
- Customer ratings and review feed.
- *"You May Also Like"* recommended pairings grid.
- Full `Product` and `AggregateRating` JSON-LD schema for Google Shopping.

### Shopping Bag (`cart.php`) & Slide-Out Drawer:
- Free delivery progress bar indicating amount remaining for complimentary white-glove shipping.
- Quantity adjustment (`+` / `-`) and item removal with live subtotal calculation.
- Coupon code application (`ELAN10`, `WELCOME500`) with live discount recalculation.

### Checkout & Order System (`checkout.php`):
- Delivery address form with Indian state and pincode validation.
- Cash on Delivery (COD) and Online Payment (Razorpay) options.
- Transactional database insertion using PDO transactions.
- Stock deduction upon order placement.
- Order confirmation receipt with reference code (e.g. `ME-2026-XXXX`).

### Customer Account Portal:
- Profile management (name, phone, default shipping address, password change).
- Order history with status tracking badges (*Pending*, *Confirmed*, *Packed*, *Shipped*, *Delivered*).
- Guest order tracking lookup using order reference and email.
- Wishlist page with 1-click move to shopping bag.

### Admin Dashboard (`/admin/`):
- Live revenue, order counts, pending fulfillment, customer registrations, and product statistics.
- Full CRUD for products with image upload support and category assignments.
- Category management with active status toggles.
- Order review screen with ability to change fulfillment and payment statuses and assign courier AWB tracking numbers.
- Promo coupon code generator.
- Client reviews moderation (approve / hide / delete).
- Newsletter subscribers table with 1-click CSV export.
- Store settings editor (contact details, shipping thresholds, Razorpay keys).

---

## 9. Security & Optimization Measures

- **Prepared Statements:** 100% of database queries utilize PDO prepared statements with parameter binding to prevent SQL injection.
- **XSS Prevention:** All dynamic customer-facing strings are sanitized with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- **CSRF Tokens:** All forms (checkout, login, registration, profile, product management) utilize session-bound cryptographic CSRF tokens.
- **Session Security:** `session.cookie_httponly = 1`, `session.use_only_cookies = 1`, and `SameSite = Lax`.
- **Password Protection:** Passwords securely hashed with standard `password_hash($pass, PASSWORD_BCRYPT)`.
- **File Upload Protection:** File type validation (JPEG, PNG, WebP) and `getimagesize()` verification to prevent malicious file uploads.
- **Zero-Dependency Vanilla JS:** No bulky frameworks; high-speed performance on mobile networks.
- **Server Caching & Compression:** Apache `.htaccess` includes Gzip compression rules and 30-day browser caching headers for static assets.
- **Clean Fallbacks:** If MySQL is not connected or in testing, mock data prevents crashes so the boutique displays smoothly.

---

## 10. Sample Seed Silhouettes in Database

1. **Ivory Silk Draped Saree** — ₹8,490 *(Old: ₹10,490)* — Mulberry silk with hand-sewn pearl work.
2. **Midnight Embroidered Anarkali** — ₹6,990 *(Old: ₹8,490)* — 32-kali silk georgette with silver bugle beads.
3. **Rose Gold Organza Saree** — ₹9,890 *(Old: ₹12,500)* — Tissue organza with scalloped hand embroidery.
4. **Pearl White Designer Kurti Set** — ₹4,990 *(Old: ₹5,990)* — Chanderi silk with hand-painted dupatta.
5. **Burgundy Evening Dress** — ₹5,490 *(Old: ₹6,990)* — Double-faced Japanese satin with asymmetric cowl drape.
6. **Champagne Embellished Blouse** — ₹3,990 *(Old: ₹4,890)* — High-neck cutdana beaded statement separate.
7. **Emerald Festive Co-ord Set** — ₹5,990 *(Old: ₹7,490)* — Crepe silk blazer with gold bullion tilla work.
8. **Black Sculpted Party Dress** — ₹6,490 *(Old: ₹7,990)* — Structured shoulder midi dress with side ruching.
9. **Crimson Banarasi Bridal Saree** — ₹14,990 *(Old: ₹18,500)* — Katan silk Banarasi with real gold zari jaal.
10. **Blush Pink Tiered Georgette Gown** — ₹7,890 *(Old: ₹9,990)* — Corset bodice with 25-meter micro-pleated flare.
11. **Vintage Brocade Tailored Kurti** — ₹5,290 *(Old: ₹6,490)* — Raw silk with antique booti motifs & potli buttons.
12. **Royal Sapphire Velvet Kaftan** — ₹8,990 *(Old: ₹11,200)* — Micro-velvet with dull gold marodi zardozi.

---

&copy; 2026 MAISON ÉLAN Haute Couture. Designed to Be Remembered.
