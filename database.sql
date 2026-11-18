-- ========================================================
-- MAISON ÉLAN - Designer Fashion Boutique
-- Database Schema & Realistic Seed Data
-- Designed for MySQL 8.0+ / Hostinger Shared Hosting
-- Collation: utf8mb4_unicode_ci
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `newsletter_subscribers`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `wishlist`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `admin_users`;
DROP TABLE IF EXISTS `settings`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- 1. Table structure for `admin_users`
-- --------------------------------------------------------
CREATE TABLE `admin_users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('superadmin', 'manager', 'editor') NOT NULL DEFAULT 'superadmin',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_admin_login` (`username`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Table structure for `users` (Customers)
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NULL,
  `address` TEXT NULL,
  `city` VARCHAR(100) NULL,
  `state` VARCHAR(100) NULL,
  `pincode` VARCHAR(10) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Table structure for `categories`
-- --------------------------------------------------------
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `image_url` VARCHAR(500) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_category_slug` (`slug`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Table structure for `products`
-- --------------------------------------------------------
CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `short_description` VARCHAR(300) NOT NULL,
  `description` LONGTEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `sale_price` DECIMAL(10,2) NULL,
  `stock_quantity` INT NOT NULL DEFAULT 10,
  `sizes` VARCHAR(150) NOT NULL DEFAULT 'XS,S,M,L,XL',
  `colors` VARCHAR(150) NOT NULL DEFAULT 'Ivory,Gold',
  `fabric_care` TEXT NULL,
  `shipping_info` TEXT NULL,
  `return_policy` TEXT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_bestseller` TINYINT(1) NOT NULL DEFAULT 0,
  `is_new` TINYINT(1) NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `rating` DECIMAL(2,1) NOT NULL DEFAULT 5.0,
  `reviews_count` INT NOT NULL DEFAULT 0,
  `views_count` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_prod_category` (`category_id`),
  INDEX `idx_prod_slug` (`slug`),
  INDEX `idx_prod_flags` (`is_active`, `is_featured`, `is_bestseller`, `is_new`),
  INDEX `idx_prod_price` (`price`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Table structure for `product_images`
-- --------------------------------------------------------
CREATE TABLE `product_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `image_url` VARCHAR(500) NOT NULL,
  `alt_text` VARCHAR(255) NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `is_hover` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_img_prod` (`product_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Table structure for `orders`
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(32) NOT NULL UNIQUE,
  `user_id` INT UNSIGNED NULL,
  `customer_name` VARCHAR(120) NOT NULL,
  `customer_email` VARCHAR(120) NOT NULL,
  `customer_phone` VARCHAR(25) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `city` VARCHAR(80) NOT NULL,
  `state` VARCHAR(80) NOT NULL,
  `pincode` VARCHAR(12) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `shipping_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `coupon_code` VARCHAR(50) NULL,
  `grand_total` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('cod', 'razorpay') NOT NULL DEFAULT 'cod',
  `payment_status` ENUM('Pending', 'Paid', 'Failed', 'Refunded', 'COD') NOT NULL DEFAULT 'COD',
  `razorpay_order_id` VARCHAR(100) NULL,
  `razorpay_payment_id` VARCHAR(100) NULL,
  `order_status` ENUM('Pending', 'Confirmed', 'Packed', 'Shipped', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `tracking_number` VARCHAR(100) NULL,
  `courier_name` VARCHAR(100) NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_order_user` (`user_id`),
  INDEX `idx_order_status` (`order_status`),
  INDEX `idx_order_num` (`order_number`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. Table structure for `order_items`
-- --------------------------------------------------------
CREATE TABLE `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `product_image` VARCHAR(500) NULL,
  `size` VARCHAR(20) NOT NULL DEFAULT 'Free Size',
  `color` VARCHAR(50) NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `total_price` DECIMAL(10,2) NOT NULL,
  INDEX `idx_item_order` (`order_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. Table structure for `cart`
-- --------------------------------------------------------
CREATE TABLE `cart` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(64) NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `size` VARCHAR(20) NOT NULL DEFAULT 'Free Size',
  `color` VARCHAR(50) NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_cart_session` (`session_id`),
  INDEX `idx_cart_user` (`user_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 9. Table structure for `wishlist`
-- --------------------------------------------------------
CREATE TABLE `wishlist` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(64) NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_wishlist_session_prod` (`session_id`, `product_id`),
  INDEX `idx_wish_user` (`user_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 10. Table structure for `coupons`
-- --------------------------------------------------------
CREATE TABLE `coupons` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_type` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` DECIMAL(10,2) NOT NULL,
  `min_order_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` DECIMAL(10,2) NULL,
  `valid_from` DATE NOT NULL,
  `valid_until` DATE NOT NULL,
  `usage_limit` INT NOT NULL DEFAULT 1000,
  `times_used` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 11. Table structure for `reviews`
-- --------------------------------------------------------
CREATE TABLE `reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_city` VARCHAR(80) NULL,
  `rating` TINYINT NOT NULL DEFAULT 5,
  `title` VARCHAR(150) NULL,
  `comment` TEXT NOT NULL,
  `is_verified_buyer` TINYINT(1) NOT NULL DEFAULT 1,
  `is_approved` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_review_prod` (`product_id`, `is_approved`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 12. Table structure for `newsletter_subscribers`
-- --------------------------------------------------------
CREATE TABLE `newsletter_subscribers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `status` ENUM('active', 'unsubscribed') NOT NULL DEFAULT 'active',
  `subscribed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 13. Table structure for `settings`
-- --------------------------------------------------------
CREATE TABLE `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(60) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- SEED DATA
-- ========================================================

-- Admin User: Username: admin, Password: Admin@12345
INSERT INTO `admin_users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `is_active`, `created_at`) VALUES
(1, 'admin', 'admin@maisonelan.com', '$2y$10$.P5XqdX0v12yC7opnLQ34eBHHdFCq7qeDNPtXqs0rBdc5ZbI0ByUi', 'Maison Élan Atelier', 'superadmin', 1, NOW());

-- Customer Test Account: Password: Customer@123
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `city`, `state`, `pincode`, `created_at`) VALUES
(1, 'Aanya Singhania', 'aanya@example.com', '$2y$10$M0wgzwKKITfNNIn6JI4RherbJKVRxIVMQNQ1UDvD3iIgFLQFTJh.i', '+91 98200 12345', 'B-14, Gulmohar Avenue, Juhu', 'Mumbai', 'Maharashtra', '400049', NOW());

-- Store Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('store_name', 'MAISON ÉLAN'),
('store_tagline', 'Designed to Be Remembered.'),
('store_email', 'concierge@maisonelan.com'),
('store_phone', '+91 98765 43210'),
('store_address', '42 Heritage Boulevard, Chanakyapuri, New Delhi 110021'),
('free_shipping_threshold', '5000'),
('flat_shipping_rate', '250'),
('currency_symbol', '₹');

-- Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image_url`, `sort_order`, `is_active`) VALUES
(1, 'Sarees', 'sarees', 'Pure silks, sheer organzas, and handcrafted drape masterworks woven with heritage zardozi.', 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80', 1, 1),
(2, 'Dresses', 'dresses', 'Architectural silhouettes, sculpted evening gowns, and fluid contemporary silhouettes.', 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1000&q=80', 2, 1),
(3, 'Occasion Wear', 'occasion-wear', 'Anarkalis, embroidered lehengas, and regal festive ensembles for unforgettable galas.', 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1000&q=80', 3, 1),
(4, 'Everyday Luxe', 'everyday-luxe', 'Tailored co-ord sets, modal tunics, and modern silk separates crafted for elevated living.', 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1000&q=80', 4, 1),
(5, 'Kurtis & Sets', 'kurtis', 'Artisanal block-printed chanderis and raw silk kurti sets with delicate pita embroidery.', 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=80', 5, 1),
(6, 'Wedding Collection', 'wedding-collection', 'The Bridal & Trousseau edit. Heirloom silks, real zari motifs, and regal silhouettes.', 'https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=1000&q=80', 6, 1);

-- Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `short_description`, `description`, `price`, `sale_price`, `stock_quantity`, `sizes`, `colors`, `fabric_care`, `shipping_info`, `return_policy`, `is_featured`, `is_bestseller`, `is_new`, `is_active`, `rating`, `reviews_count`) VALUES
(1, 1, 'Ivory Silk Draped Saree', 'ivory-silk-draped-saree', 'ME-SAR-001', 'Hand-loomed mulberry silk with delicate pearl and antique gold beadwork along the pleated pallu.', 'An ode to understated poise, the Ivory Silk Draped Saree is woven from pure mulberry silk with a luminous subtle sheen. Featuring meticulously handcrafted zardozi fringes, pearl-encrusted border detailing, and an architectural pre-pleated drape, this saree moves like liquid poetry. Accompanied by an unstitched raw silk blouse piece with coordinating hand embroidery.', 8490.00, 10490.00, 14, 'XS,S,M,L,XL,Free Size', 'Ivory,Champagne Gold', 'Dry clean only. Store wrapped in soft muslin cloth. Do not spray perfumes directly onto metal embroidery.', 'Complimentary express courier within India (3-5 business days). International priority delivery available.', 'Eligible for return or exchange within 7 days of delivery in pristine unworn condition with boutique tags intact.', 1, 1, 1, 1, 4.9, 18),

(2, 3, 'Midnight Embroidered Anarkali', 'midnight-embroidered-anarkali', 'ME-ANK-002', 'Deep navy silk georgette floor-length anarkali accented with silver threadwork and bugle beads.', 'Evoking the velvet mysteries of starlit evenings, our Midnight Embroidered Anarkali features a 32-kali flare tailored from floaty silk georgette. The bodice is laden with fine resham threadwork, cutdana embellishments, and tonal sequin clusters that catch ambient chandelier light. Layered over a soft satin lining and finished with an organza dupatta edged in scalloped gota.', 6990.00, 8490.00, 10, 'S,M,L,XL', 'Midnight Navy,Cobalt', 'Professional dry clean only. Steam iron on reverse at low heat.', 'Dispatches within 48 hours. Delivered via signature courier.', 'Easy 7-day boutique returns policy.', 1, 1, 1, 1, 5.0, 24),

(3, 1, 'Rose Gold Organza Saree', 'rose-gold-organza-saree', 'ME-SAR-003', 'Whisper-light tissue organza with metallic rose gold sheen and intricate scalloped hand embroidery.', 'Woven on fine looms using metallic tissue threads and gossamer silk, this Rose Gold Organza Saree radiates soft warmth. The perimeter is hand-cut in floral scallops embellished with fine dabka, pearls, and micro sequins. Paired with a complementary rose-gold tissue blouse fabric for a seamless ethereal presence.', 9890.00, 12500.00, 8, 'Free Size', 'Rose Gold,Dusty Blush', 'Specialist dry clean only. Avoid moisture and direct sunlight during storage.', 'Insured priority dispatch within 3 days.', 'Exchange allowed within 7 days of receipt.', 1, 0, 1, 1, 4.8, 12),

(4, 5, 'Pearl White Designer Kurti Set', 'pearl-white-designer-kurti-set', 'ME-KUR-004', 'Pure Chanderi silk kurti paired with tailored cigarette pants and a hand-painted floral dupatta.', 'A study in quiet luxury, this pearl-white ensemble is cut from artisanal Chanderi silk with subtle self-stripes. The notched neckline features subtle pearl buttons and chikankari-inspired tonal stitches. Paired with matching silk pants featuring hem scalloping and a featherweight Kota Doria dupatta with gilded foil accents.', 4990.00, 5990.00, 20, 'XS,S,M,L,XL', 'Pearl White,Soft Ivory', 'Dry clean recommended. Gentle cold hand wash separately for pants.', 'Dispatches next working day.', '7-day hassle-free exchange.', 1, 1, 1, 1, 4.9, 31),

(5, 2, 'Burgundy Evening Dress', 'burgundy-evening-dress', 'ME-DRS-005', 'Sculptural asymmetric cowl-neck gown cut from double-faced Japanese satin in deep wine burgundy.', 'Designed for celebratory galas and candlelit evenings, this evening gown embodies minimalist drama. The neckline drapes effortlessly across the collarbone while the bias-cut silhouette contours gracefully through the hip, falling into an elongated puddle train. Fully lined in matching silk habotai.', 5490.00, 6990.00, 12, 'XS,S,M,L', 'Burgundy,Deep Wine', 'Dry clean only. Cool iron over a press cloth.', 'Express 2-4 day delivery.', '7-day returns for full refund or store credit.', 1, 0, 1, 1, 4.7, 9),

(6, 4, 'Champagne Embellished Blouse', 'champagne-embellished-blouse', 'ME-BLS-006', 'High-neck padded blouse drenched in antique cutdana and champagne glass beads with a keyhole back.', 'The ultimate statement separate. Our Champagne Embellished Blouse is handcrafted by master karigars over 40 hours. Designed with built-in artisan cups, a jewel neckline, and a statement back cutout fastened with gold filigree buttons. Seamlessly elevates classic sarees, tiered lehengas, or high-waist palazzo trousers.', 3990.00, 4890.00, 16, '32,34,36,38,40', 'Champagne,Antique Gold', 'Specialist spot clean or dry clean only.', 'Same-day packaging and fast dispatch.', '7-day size exchange guarantee.', 1, 1, 1, 1, 4.9, 15),

(7, 4, 'Emerald Festive Co-ord Set', 'emerald-festive-co-ord-set', 'ME-CRD-007', 'Rich emerald green crepe silk blazer with flared trousers featuring gold bullion hand embroidery.', 'Modern power dressing meets festive opulence. Cut from heavyweight crepe de chine, this tailored jacket features peaked lapels highlighted with antique tilla bullion embroidery. The coordinating high-waisted palazzo trousers fall cleanly with crisp pressed creases, elongating the silhouette effortlessly.', 5990.00, 7490.00, 11, 'S,M,L,XL', 'Emerald Green,Forest Green', 'Dry clean only.', 'Complimentary shipping across India.', 'Returns accepted within 7 days.', 1, 1, 1, 1, 5.0, 19),

(8, 2, 'Black Sculpted Party Dress', 'black-sculpted-party-dress', 'ME-DRS-008', 'Architectural midi dress with structured shoulder pads, side ruching, and a dramatic leg slit.', 'The definitive evening silhouette. Cut from dense Italian ponte knit that holds its shape while offering flattering stretch. Features sharp razor-sharp shoulder construction, meticulous micro-pleated gathers across the waist, and an alluring side slit tailored for poised movement.', 6490.00, 7990.00, 15, 'XS,S,M,L', 'Noir Black,Charcoal', 'Dry clean only.', 'Fast dispatch within 24 hours.', '7-day returns with boutique tag intact.', 1, 1, 1, 1, 4.8, 22),

(9, 6, 'Crimson Banarasi Bridal Saree', 'crimson-banarasi-bridal-saree', 'ME-SAR-009', 'Pure katan silk Banarasi handloom saree with real gold zari floral jaal and meenakari motifs.', 'A treasured heirloom designed to be cherished across generations. Hand-woven in Varanasi over three months, this crimson saree features real zari gold threadwork intertwining floral jaals with delicate jewel-toned meenakari accents. The pallu showcases traditional kadhwa craftsmanship. Accompanied by a bespoke certificate of handloom authenticity.', 14990.00, 18500.00, 5, 'Free Size', 'Crimson Red,Royal Maroon', 'Dry clean only. Store wrapped in pure unbleached cotton.', 'Signature white-glove courier with tamper-evident seal.', 'Due to heirloom craftsmanship, exchange only within 5 days.', 1, 1, 0, 1, 5.0, 14),

(10, 2, 'Blush Pink Tiered Georgette Gown', 'blush-pink-tiered-georgette-gown', 'ME-GWN-010', 'Romantic floor-length gown featuring cascaded micro-tiers, corset bodice, and hand-embroidered waist.', 'Soft and ethereal, this blush pink gown is constructed from 25 meters of micro-pleated georgette. The structured boned corset bodice ensures a snug, flattering fit, while the layered tiers float weightlessly with every step. Finished with delicate hand-embroidered crystal dewdrops along the sweetheart neckline.', 7890.00, 9990.00, 9, 'XS,S,M,L', 'Blush Pink,Powder Peach', 'Specialist dry clean only.', 'Dispatches within 3 days.', '7-day boutique returns.', 0, 1, 1, 1, 4.9, 11),

(11, 5, 'Vintage Brocade Tailored Kurti', 'vintage-brocade-tailored-kurti', 'ME-KUR-011', 'Raw silk kurti in mustard gold with woven antique zari motifs and potli button detailing.', 'Embodying vintage royal court splendor, this mustard gold kurti is woven from pure raw silk with heritage booti motifs. Features a Mandarin collar, three-quarter sleeves finished with gota patti piping, and handmade potli buttons cascading down the front slit. Pairs beautifully with cigarette trousers or crushed silk skirts.', 5290.00, 6490.00, 18, 'S,M,L,XL,XXL', 'Mustard Gold,Rust Ochre', 'Dry clean only.', 'Shipped in premium branded garment packaging.', '7-day returns accepted.', 0, 1, 1, 1, 4.7, 8),

(12, 3, 'Royal Sapphire Velvet Kaftan', 'royal-sapphire-velvet-kaftan', 'ME-KFT-012', 'Plush micro-velvet kaftan with elaborate marodi and zardozi embroidery along the neckline and cuffs.', 'Understated regality for winter soirees. Cut from sumptuous royal sapphire micro-velvet, this flowy kaftan features artisanal marodi embroidery in antique dull gold. An internal drawstring allows you to cinch the waist for a structured fit or leave it relaxed for fluid glamour.', 8990.00, 11200.00, 7, 'Free Size', 'Royal Sapphire,Deep Teal', 'Specialist dry clean only.', 'Express complimentary insured shipping.', 'Exchange or return within 7 days.', 1, 1, 0, 1, 5.0, 16);

-- Product Images (Primary and Alternate Hover Images for Every Single Item)
INSERT INTO `product_images` (`product_id`, `image_url`, `alt_text`, `is_primary`, `is_hover`, `sort_order`) VALUES
-- Product 1: Ivory Silk Draped Saree
(1, 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80', 'Ivory Silk Draped Saree Front Drape', 1, 0, 1),
(1, 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=80', 'Ivory Silk Draped Saree Pallu Detail', 0, 1, 2),
(1, 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1000&q=80', 'Ivory Silk Draped Saree Fabric Texture', 0, 0, 3),

-- Product 2: Midnight Embroidered Anarkali
(2, 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1000&q=80', 'Midnight Embroidered Anarkali Full Flare', 1, 0, 1),
(2, 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80', 'Midnight Embroidered Anarkali Bodice Work', 0, 1, 2),

-- Product 3: Rose Gold Organza Saree
(3, 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=80', 'Rose Gold Organza Saree Front View', 1, 0, 1),
(3, 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80', 'Rose Gold Organza Saree Sheer Border', 0, 1, 2),

-- Product 4: Pearl White Designer Kurti Set
(4, 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1000&q=80', 'Pearl White Designer Kurti Set Model View', 1, 0, 1),
(4, 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=80', 'Pearl White Kurti Embroidery Close Up', 0, 1, 2),

-- Product 5: Burgundy Evening Dress
(5, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1000&q=80', 'Burgundy Evening Dress Front Silhouette', 1, 0, 1),
(5, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1000&q=80', 'Burgundy Evening Dress Cowl Drape', 0, 1, 2),

-- Product 6: Champagne Embellished Blouse
(6, 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=1000&q=80', 'Champagne Embellished Blouse Studio View', 1, 0, 1),
(6, 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80', 'Champagne Embellished Blouse Back Detail', 0, 1, 2),

-- Product 7: Emerald Festive Co-ord Set
(7, 'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=1000&q=80', 'Emerald Festive Co-ord Set Editorial Shot', 1, 0, 1),
(7, 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1000&q=80', 'Emerald Co-ord Lapel Embroidery', 0, 1, 2),

-- Product 8: Black Sculpted Party Dress
(8, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1000&q=80', 'Black Sculpted Party Dress Runway Angle', 1, 0, 1),
(8, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1000&q=80', 'Black Sculpted Party Dress Side Slit Detail', 0, 1, 2),

-- Product 9: Crimson Banarasi Bridal Saree
(9, 'https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=1000&q=80', 'Crimson Banarasi Bridal Saree Regal Pose', 1, 0, 1),
(9, 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80', 'Crimson Banarasi Zari Weave Pallu', 0, 1, 2),

-- Product 10: Blush Pink Tiered Georgette Gown
(10, 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1000&q=80', 'Blush Pink Tiered Georgette Gown Front', 1, 0, 1),
(10, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1000&q=80', 'Blush Pink Gown Twirl Motion', 0, 1, 2),

-- Product 11: Vintage Brocade Tailored Kurti
(11, 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=80', 'Vintage Brocade Tailored Kurti Heritage View', 1, 0, 1),
(11, 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1000&q=80', 'Vintage Brocade Kurti Fabric Weave', 0, 1, 2),

-- Product 12: Royal Sapphire Velvet Kaftan
(12, 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1000&q=80', 'Royal Sapphire Velvet Kaftan Elegance', 1, 0, 1),
(12, 'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=1000&q=80', 'Royal Sapphire Velvet Kaftan Neckline Art', 0, 1, 2);

-- Sample Reviews
INSERT INTO `reviews` (`product_id`, `user_id`, `customer_name`, `customer_city`, `rating`, `title`, `comment`, `is_verified_buyer`, `is_approved`, `created_at`) VALUES
(1, 1, 'Devika Malhotra', 'New Delhi', 5, 'Every detail felt genuinely luxurious', 'Every detail felt luxurious, from the bespoke gold-embossed packaging to the outfit itself. The silk drape falls with so much grace. Truly elevated couture.', 1, 1, NOW() - INTERVAL 12 DAY),
(1, NULL, 'Ananya Roy Chowdhury', 'Kolkata', 5, 'Exceeded all expectations', 'Absolutely loved the fit and finish. The saree looked even better in person. The antique gold pearl work caught everyone’s admiration at my sister’s sangeet.', 1, 1, NOW() - INTERVAL 18 DAY),
(2, NULL, 'Radhika Merchant', 'Mumbai', 5, 'Flawless atelier craftsmanship', 'One of the best boutique shopping experiences I have had. The 32-kali flare on this anarkali feels weightless yet grand.', 1, 1, NOW() - INTERVAL 5 DAY),
(4, 1, 'Meera Sen', 'Bengaluru', 5, 'Chic everyday elegance', 'The Chanderi fabric is so breathable and soft. The hand-painted dupatta makes it look worth thrice the price.', 1, 1, NOW() - INTERVAL 3 DAY),
(7, NULL, 'Shreya Tandon', 'Jaipur', 5, 'Impeccable cut and color', 'The emerald hue is breathtaking. Got countless compliments throughout the cocktail evening. Will definitely order from Maison Élan again.', 1, 1, NOW() - INTERVAL 8 DAY);

-- Sample Coupons
INSERT INTO `coupons` (`code`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `valid_from`, `valid_until`, `usage_limit`, `times_used`, `is_active`) VALUES
('ELAN10', 'percentage', 10.00, 3000.00, 2000.00, '2025-01-01', '2027-12-31', 500, 24, 1),
('WELCOME500', 'fixed', 500.00, 4000.00, 500.00, '2025-01-01', '2027-12-31', 1000, 68, 1),
('ROYAL15', 'percentage', 15.00, 8000.00, 3500.00, '2025-01-01', '2027-12-31', 200, 11, 1);

-- Sample Newsletter Subscribers
INSERT INTO `newsletter_subscribers` (`email`, `status`, `subscribed_at`) VALUES
('tanya.sharma@example.com', 'active', NOW() - INTERVAL 15 DAY),
('priya.deshmukh@example.com', 'active', NOW() - INTERVAL 10 DAY),
('simran.kapoor@example.com', 'active', NOW() - INTERVAL 2 DAY);

-- Sample Demo Order
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `city`, `state`, `pincode`, `subtotal`, `shipping_fee`, `discount_amount`, `coupon_code`, `grand_total`, `payment_method`, `payment_status`, `order_status`, `notes`, `created_at`) VALUES
(1, 'ME-2026-1001', 1, 'Aanya Singhania', 'aanya@example.com', '+91 98200 12345', 'B-14, Gulmohar Avenue, Juhu', 'Mumbai', 'Maharashtra', '400049', 8490.00, 0.00, 849.00, 'ELAN10', 7641.00, 'cod', 'COD', 'Confirmed', 'Please ring the doorbell twice upon delivery.', NOW() - INTERVAL 2 DAY);

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `product_image`, `size`, `color`, `unit_price`, `quantity`, `total_price`) VALUES
(1, 1, 'Ivory Silk Draped Saree', 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80', 'Free Size', 'Ivory', 8490.00, 1, 8490.00);
