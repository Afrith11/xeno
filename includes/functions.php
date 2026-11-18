<?php
/**
 * MAISON ÉLAN - Core Helper Functions
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Escape HTML output to prevent XSS
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format price in Indian Rupee format (e.g. ₹8,490)
 */
function format_price(float|int|string $amount): string {
    $num = (float)$amount;
    // Format with Indian comma placement
    $parts = explode('.', number_format($num, 2, '.', ''));
    $whole = $parts[0];
    
    if (strlen($whole) > 3) {
        $lastThree = substr($whole, -3);
        $otherNumbers = substr($whole, 0, -3);
        $otherNumbers = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $otherNumbers);
        $formatted = $otherNumbers . ',' . $lastThree;
    } else {
        $formatted = $whole;
    }
    
    return CURRENCY_SYMBOL . $formatted;
}

/**
 * Calculate discount percentage
 */
function calculate_discount_percent(float $regular_price, ?float $sale_price): int {
    if (!$sale_price || $sale_price >= $regular_price || $regular_price <= 0) {
        return 0;
    }
    return (int) round((($regular_price - $sale_price) / $regular_price) * 100);
}

/**
 * Generate URL slug from title
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * CSRF Protection
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Flash messaging system
 */
function set_flash_message(string $type, string $message): void {
    $_SESSION['flash_messages'][] = [
        'type' => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message
    ];
}

function get_flash_messages(): array {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

function render_flash_messages(): string {
    $messages = get_flash_messages();
    if (empty($messages)) {
        return '';
    }
    
    $html = '<div class="flash-messages-container">';
    foreach ($messages as $msg) {
        $type = e($msg['type']);
        $text = e($msg['message']);
        $html .= "<div class=\"flash-alert flash-{$type}\" role=\"alert\">";
        $html .= "<span>{$text}</span>";
        $html .= "<button type=\"button\" class=\"flash-close\" onclick=\"this.parentElement.remove()\">&times;</button>";
        $html .= "</div>";
    }
    $html .= '</div>';
    return $html;
}

/**
 * Safe redirect helper
 */
function redirect(string $url): void {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    }
    echo "<script>window.location.href='" . addslashes($url) . "';</script>";
    exit;
}

/**
 * Get guest / user session identifier for cart & wishlist
 */
function get_session_id(): string {
    if (empty($_SESSION['guest_session_id'])) {
        $_SESSION['guest_session_id'] = 'guest_' . bin2hex(random_bytes(16));
    }
    return $_SESSION['guest_session_id'];
}

/**
 * Render luxury rating stars
 */
function render_rating_stars(float $rating, int $reviewsCount = 0): string {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    
    $html = '<div class="star-rating" title="' . number_format($rating, 1) . ' out of 5 stars">';
    for ($i = 0; $i < $full; $i++) {
        $html .= '<span class="star star-filled">★</span>';
    }
    if ($half) {
        $html .= '<span class="star star-half">★</span>';
    }
    for ($i = 0; $i < $empty; $i++) {
        $html .= '<span class="star star-empty">☆</span>';
    }
    if ($reviewsCount > 0) {
        $html .= '<span class="rating-count">(' . $reviewsCount . ')</span>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Fallback sample products when DB is not yet populated
 */
function get_sample_products(): array {
    return [
        [
            'id' => 1,
            'category_id' => 1,
            'category_name' => 'Sarees',
            'category_slug' => 'sarees',
            'name' => 'Ivory Silk Draped Saree',
            'slug' => 'ivory-silk-draped-saree',
            'sku' => 'ME-SAR-001',
            'short_description' => 'Hand-loomed mulberry silk with delicate pearl and antique gold beadwork along the pleated pallu.',
            'description' => 'An ode to understated poise, the Ivory Silk Draped Saree is woven from pure mulberry silk with a luminous subtle sheen. Featuring meticulously handcrafted zardozi fringes, pearl-encrusted border detailing, and an architectural pre-pleated drape, this saree moves like liquid poetry.',
            'price' => 8490.00,
            'sale_price' => 10490.00,
            'stock_quantity' => 14,
            'sizes' => 'XS,S,M,L,XL,Free Size',
            'colors' => 'Ivory,Champagne Gold',
            'fabric_care' => 'Dry clean only. Store wrapped in soft muslin cloth.',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'is_new' => 1,
            'rating' => 4.9,
            'reviews_count' => 18,
            'primary_image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80',
            'hover_image' => 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=80'
        ],
        [
            'id' => 2,
            'category_id' => 3,
            'category_name' => 'Occasion Wear',
            'category_slug' => 'occasion-wear',
            'name' => 'Midnight Embroidered Anarkali',
            'slug' => 'midnight-embroidered-anarkali',
            'sku' => 'ME-ANK-002',
            'short_description' => 'Deep navy silk georgette floor-length anarkali accented with silver threadwork and bugle beads.',
            'description' => 'Evoking the velvet mysteries of starlit evenings, our Midnight Embroidered Anarkali features a 32-kali flare tailored from floaty silk georgette. Layered over a soft satin lining and finished with an organza dupatta edged in scalloped gota.',
            'price' => 6990.00,
            'sale_price' => 8490.00,
            'stock_quantity' => 10,
            'sizes' => 'S,M,L,XL',
            'colors' => 'Midnight Navy,Cobalt',
            'fabric_care' => 'Professional dry clean only. Steam iron on reverse.',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'is_new' => 1,
            'rating' => 5.0,
            'reviews_count' => 24,
            'primary_image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1000&q=80',
            'hover_image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80'
        ],
        [
            'id' => 3,
            'category_id' => 1,
            'category_name' => 'Sarees',
            'category_slug' => 'sarees',
            'name' => 'Rose Gold Organza Saree',
            'slug' => 'rose-gold-organza-saree',
            'sku' => 'ME-SAR-003',
            'short_description' => 'Whisper-light tissue organza with metallic rose gold sheen and intricate scalloped hand embroidery.',
            'description' => 'Woven on fine looms using metallic tissue threads and gossamer silk, this Rose Gold Organza Saree radiates soft warmth. Embellished with fine dabka, pearls, and micro sequins.',
            'price' => 9890.00,
            'sale_price' => 12500.00,
            'stock_quantity' => 8,
            'sizes' => 'Free Size',
            'colors' => 'Rose Gold,Dusty Blush',
            'fabric_care' => 'Specialist dry clean only.',
            'is_featured' => 1,
            'is_bestseller' => 0,
            'is_new' => 1,
            'rating' => 4.8,
            'reviews_count' => 12,
            'primary_image' => 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=80',
            'hover_image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80'
        ],
        [
            'id' => 4,
            'category_id' => 5,
            'category_name' => 'Kurtis & Sets',
            'category_slug' => 'kurtis',
            'name' => 'Pearl White Designer Kurti Set',
            'slug' => 'pearl-white-designer-kurti-set',
            'sku' => 'ME-KUR-004',
            'short_description' => 'Pure Chanderi silk kurti paired with tailored cigarette pants and a hand-painted floral dupatta.',
            'description' => 'A study in quiet luxury, this pearl-white ensemble is cut from artisanal Chanderi silk with subtle self-stripes. The notched neckline features subtle pearl buttons and chikankari-inspired tonal stitches.',
            'price' => 4990.00,
            'sale_price' => 5990.00,
            'stock_quantity' => 20,
            'sizes' => 'XS,S,M,L,XL',
            'colors' => 'Pearl White,Soft Ivory',
            'fabric_care' => 'Dry clean recommended. Gentle cold hand wash.',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'is_new' => 1,
            'rating' => 4.9,
            'reviews_count' => 31,
            'primary_image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1000&q=80',
            'hover_image' => 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=80'
        ],
        [
            'id' => 5,
            'category_id' => 2,
            'category_name' => 'Dresses',
            'category_slug' => 'dresses',
            'name' => 'Burgundy Evening Dress',
            'slug' => 'burgundy-evening-dress',
            'sku' => 'ME-DRS-005',
            'short_description' => 'Sculptural asymmetric cowl-neck gown cut from double-faced Japanese satin in deep wine burgundy.',
            'description' => 'Designed for celebratory galas and candlelit evenings, this evening gown embodies minimalist drama. The neckline drapes effortlessly across the collarbone while the bias-cut silhouette contours gracefully.',
            'price' => 5490.00,
            'sale_price' => 6990.00,
            'stock_quantity' => 12,
            'sizes' => 'XS,S,M,L',
            'colors' => 'Burgundy,Deep Wine',
            'fabric_care' => 'Dry clean only. Cool iron over a press cloth.',
            'is_featured' => 1,
            'is_bestseller' => 0,
            'is_new' => 1,
            'rating' => 4.7,
            'reviews_count' => 9,
            'primary_image' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1000&q=80',
            'hover_image' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1000&q=80'
        ],
        [
            'id' => 6,
            'category_id' => 4,
            'category_name' => 'Everyday Luxe',
            'category_slug' => 'everyday-luxe',
            'name' => 'Champagne Embellished Blouse',
            'slug' => 'champagne-embellished-blouse',
            'sku' => 'ME-BLS-006',
            'short_description' => 'High-neck padded blouse drenched in antique cutdana and champagne glass beads with a keyhole back.',
            'description' => 'The ultimate statement separate. Our Champagne Embellished Blouse is handcrafted by master karigars over 40 hours. Seamlessly elevates classic sarees, tiered lehengas, or high-waist palazzo trousers.',
            'price' => 3990.00,
            'sale_price' => 4890.00,
            'stock_quantity' => 16,
            'sizes' => '32,34,36,38,40',
            'colors' => 'Champagne,Antique Gold',
            'fabric_care' => 'Specialist spot clean or dry clean only.',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'is_new' => 1,
            'rating' => 4.9,
            'reviews_count' => 15,
            'primary_image' => 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=1000&q=80',
            'hover_image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80'
        ],
        [
            'id' => 7,
            'category_id' => 4,
            'category_name' => 'Everyday Luxe',
            'category_slug' => 'everyday-luxe',
            'name' => 'Emerald Festive Co-ord Set',
            'slug' => 'emerald-festive-co-ord-set',
            'sku' => 'ME-CRD-007',
            'short_description' => 'Rich emerald green crepe silk blazer with flared trousers featuring gold bullion hand embroidery.',
            'description' => 'Modern power dressing meets festive opulence. Cut from heavyweight crepe de chine, this tailored jacket features peaked lapels highlighted with antique tilla bullion embroidery.',
            'price' => 5990.00,
            'sale_price' => 7490.00,
            'stock_quantity' => 11,
            'sizes' => 'S,M,L,XL',
            'colors' => 'Emerald Green,Forest Green',
            'fabric_care' => 'Dry clean only.',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'is_new' => 1,
            'rating' => 5.0,
            'reviews_count' => 19,
            'primary_image' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=1000&q=80',
            'hover_image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1000&q=80'
        ],
        [
            'id' => 8,
            'category_id' => 2,
            'category_name' => 'Dresses',
            'category_slug' => 'dresses',
            'name' => 'Black Sculpted Party Dress',
            'slug' => 'black-sculpted-party-dress',
            'sku' => 'ME-DRS-008',
            'short_description' => 'Architectural midi dress with structured shoulder pads, side ruching, and a dramatic leg slit.',
            'description' => 'The definitive evening silhouette. Cut from dense Italian ponte knit that holds its shape while offering flattering stretch. Features sharp shoulder construction and micro-pleated gathers.',
            'price' => 6490.00,
            'sale_price' => 7990.00,
            'stock_quantity' => 15,
            'sizes' => 'XS,S,M,L',
            'colors' => 'Noir Black,Charcoal',
            'fabric_care' => 'Dry clean only.',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'is_new' => 1,
            'rating' => 4.8,
            'reviews_count' => 22,
            'primary_image' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?auto=format&fit=crop&w=1000&q=80',
            'hover_image' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1000&q=80'
        ],
        [
            'id' => 9,
            'category_id' => 6,
            'category_name' => 'Wedding Collection',
            'category_slug' => 'wedding-collection',
            'name' => 'Crimson Banarasi Bridal Saree',
            'slug' => 'crimson-banarasi-bridal-saree',
            'sku' => 'ME-SAR-009',
            'short_description' => 'Pure katan silk Banarasi handloom saree with real gold zari floral jaal and meenakari motifs.',
            'description' => 'A treasured heirloom designed to be cherished across generations. Hand-woven in Varanasi with real gold zari motifs and jewel-toned meenakari accents.',
            'price' => 14990.00,
            'sale_price' => 18500.00,
            'stock_quantity' => 5,
            'sizes' => 'Free Size',
            'colors' => 'Crimson Red,Royal Maroon',
            'fabric_care' => 'Dry clean only. Store in unbleached cotton.',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'is_new' => 0,
            'rating' => 5.0,
            'reviews_count' => 14,
            'primary_image' => 'https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=1000&q=80',
            'hover_image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80'
        ]
    ];
}

/**
 * Get categories list
 */
function get_categories(): array {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
            $cats = $stmt->fetchAll();
            if (!empty($cats)) {
                return $cats;
            }
        } catch (\Exception $e) {
            // fallback
        }
    }
    
    return [
        ['id' => 1, 'name' => 'Sarees', 'slug' => 'sarees', 'image_url' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80'],
        ['id' => 2, 'name' => 'Dresses', 'slug' => 'dresses', 'image_url' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=1000&q=80'],
        ['id' => 3, 'name' => 'Occasion Wear', 'slug' => 'occasion-wear', 'image_url' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1000&q=80'],
        ['id' => 4, 'name' => 'Everyday Luxe', 'slug' => 'everyday-luxe', 'image_url' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=1000&q=80'],
        ['id' => 5, 'name' => 'Kurtis & Sets', 'slug' => 'kurtis', 'image_url' => 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=80'],
        ['id' => 6, 'name' => 'Wedding Collection', 'slug' => 'wedding-collection', 'image_url' => 'https://images.unsplash.com/photo-1546804784-896d0dca3800?auto=format&fit=crop&w=1000&q=80']
    ];
}

/**
 * Fetch products with flexible filters
 */
function get_products(array $filters = []): array {
    $db = getDB();
    if (!$db) {
        $items = get_sample_products();
        // Simple in-memory filtering for demo fallback
        if (!empty($filters['category_slug'])) {
            $items = array_filter($items, fn($p) => ($p['category_slug'] ?? '') === $filters['category_slug']);
        }
        if (!empty($filters['search'])) {
            $q = strtolower($filters['search']);
            $items = array_filter($items, fn($p) => str_contains(strtolower($p['name']), $q) || str_contains(strtolower($p['short_description']), $q));
        }
        if (!empty($filters['limit'])) {
            $items = array_slice($items, 0, (int)$filters['limit']);
        }
        return array_values($items);
    }

    try {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_hover = 1 LIMIT 1) as hover_image
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['category_slug'])) {
            $sql .= " AND c.slug = ?";
            $params[] = $filters['category_slug'];
        }

        if (!empty($filters['is_featured'])) {
            $sql .= " AND p.is_featured = 1";
        }

        if (!empty($filters['is_bestseller'])) {
            $sql .= " AND p.is_bestseller = 1";
        }

        if (!empty($filters['is_new'])) {
            $sql .= " AND p.is_new = 1";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.short_description LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        if (!empty($filters['size'])) {
            $sql .= " AND FIND_IN_SET(?, REPLACE(p.sizes, ' ', ''))";
            $params[] = $filters['size'];
        }

        if (!empty($filters['color'])) {
            $sql .= " AND p.colors LIKE ?";
            $params[] = '%' . $filters['color'] . '%';
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_low':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_high':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'popular':
                $sql .= " ORDER BY p.views_count DESC, p.reviews_count DESC";
                break;
            case 'rating':
                $sql .= " ORDER BY p.rating DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY p.id DESC";
                break;
        }

        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        // Fallback primary image if not set in images table
        foreach ($products as &$p) {
            if (empty($p['primary_image'])) {
                $p['primary_image'] = 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=80';
            }
            if (empty($p['hover_image'])) {
                $p['hover_image'] = $p['primary_image'];
            }
        }
        return $products;
    } catch (\Exception $e) {
        error_log("Error fetching products: " . $e->getMessage());
        return get_sample_products();
    }
}

/**
 * Count total products for pagination
 */
function count_products(array $filters = []): int {
    $db = getDB();
    if (!$db) {
        return count(get_sample_products());
    }

    try {
        $sql = "SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['category_slug'])) {
            $sql .= " AND c.slug = ?";
            $params[] = $filters['category_slug'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.short_description LIKE ? OR c.name LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (\Exception $e) {
        return count(get_sample_products());
    }
}

/**
 * Get single product by slug or id
 */
function get_product(string|int $identifier): ?array {
    $db = getDB();
    if (!$db) {
        $samples = get_sample_products();
        foreach ($samples as $s) {
            if ($s['slug'] === $identifier || $s['id'] == $identifier) {
                return $s;
            }
        }
        return $samples[0] ?? null;
    }

    try {
        $is_id = is_numeric($identifier);
        $col = $is_id ? 'p.id' : 'p.slug';
        
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE {$col} = ? AND p.is_active = 1 LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$identifier]);
        $product = $stmt->fetch();

        if (!$product) {
            return null;
        }

        // Fetch all product images
        $img_stmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC");
        $img_stmt->execute([$product['id']]);
        $product['images'] = $img_stmt->fetchAll();

        // Increment view count
        $db->prepare("UPDATE products SET views_count = views_count + 1 WHERE id = ?")->execute([$product['id']]);

        return $product;
    } catch (\Exception $e) {
        error_log("Error getting product: " . $e->getMessage());
        return null;
    }
}

/**
 * Get product reviews
 */
function get_product_reviews(int $productId): array {
    $db = getDB();
    if (!$db) {
        return [
            [
                'customer_name' => 'Devika Malhotra',
                'customer_city' => 'New Delhi',
                'rating' => 5,
                'title' => 'Every detail felt genuinely luxurious',
                'comment' => 'Every detail felt luxurious, from the bespoke gold-embossed packaging to the outfit itself. The silk drape falls with so much grace. Truly elevated couture.',
                'created_at' => '2026-04-10'
            ],
            [
                'customer_name' => 'Ananya Roy Chowdhury',
                'customer_city' => 'Kolkata',
                'rating' => 5,
                'title' => 'Exceeded all expectations',
                'comment' => 'Absolutely loved the fit and finish. The saree looked even better in person. The antique gold pearl work caught everyone’s admiration.',
                'created_at' => '2026-04-05'
            ]
        ];
    }

    try {
        $stmt = $db->prepare("SELECT * FROM reviews WHERE product_id = ? AND is_approved = 1 ORDER BY id DESC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    } catch (\Exception $e) {
        return [];
    }
}

/**
 * Get active user wishlist product IDs array
 */
function get_wishlist_ids(): array {
    if (!empty($_SESSION['user_id'])) {
        $db = getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (\Exception $e) {}
        }
    }
    return $_SESSION['wishlist'] ?? [];
}
