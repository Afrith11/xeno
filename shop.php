<?php
/**
 * MAISON ÉLAN - Product Catalog (Shop)
 * Advanced Filtering, Sorting, Pagination & Mobile Filter Drawer
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/cart-functions.php';

// Parameters
$categorySlug = isset($_GET['category']) ? trim($_GET['category']) : '';
$collectionParam = isset($_GET['collection']) ? trim($_GET['collection']) : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortParam = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';
$filterNew = isset($_GET['filter']) && $_GET['filter'] === 'new';
$filterSize = isset($_GET['size']) ? trim($_GET['size']) : '';
$filterColor = isset($_GET['color']) ? trim($_GET['color']) : '';
$minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// If collection=wedding, map to wedding-collection
if ($collectionParam === 'wedding') {
    $categorySlug = 'wedding-collection';
}

$filterArgs = [
    'category_slug' => $categorySlug,
    'search' => $searchQuery,
    'sort' => $sortParam,
    'is_new' => $filterNew ? 1 : 0,
    'size' => $filterSize,
    'color' => $filterColor,
    'min_price' => $minPrice > 0 ? $minPrice : null,
    'max_price' => $maxPrice > 0 ? $maxPrice : null,
    'limit' => $perPage,
    'offset' => $offset
];

$totalProducts = count_products($filterArgs);
$products = get_products($filterArgs);
$totalPages = ceil($totalProducts / $perPage);
$categories = get_categories();

// Page metadata
$catalogTitle = "Collections & Haute Couture";
if (!empty($categorySlug)) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $categorySlug) {
            $catalogTitle = $cat['name'];
            break;
        }
    }
} elseif ($filterNew) {
    $catalogTitle = "New Arrivals 2026";
} elseif (!empty($searchQuery)) {
    $catalogTitle = "Search: “" . e($searchQuery) . "”";
}

$pageTitle = $catalogTitle . " — Boutique Catalog";
$pageDescription = "Explore the Maison Élan designer catalog. Hand-draped sarees, sculpted dresses, and festive couture.";

require_once __DIR__ . '/includes/header.php';
?>

<!-- Shop Header Banner -->
<section class="shop-header-banner">
    <div class="site-container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <span class="crumb-sep">/</span>
            <span class="crumb-current"><?= e($catalogTitle) ?></span>
        </nav>
        
        <h1 class="shop-page-title"><?= e($catalogTitle) ?></h1>
        <p class="shop-page-lead">
            Elegance articulated through luxurious textures, artisanal needlecraft, and fluid modern geometry.
        </p>
    </div>
</section>

<!-- Main Catalog Container -->
<div class="shop-main-section site-container">
    
    <!-- Mobile Filter Open Button & Sort Bar -->
    <div class="catalog-control-bar">
        <button type="button" class="btn-filter-drawer-open" id="openMobileFilterBtn">
            <svg class="filter-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <line x1="4" y1="21" x2="4" y2="14"></line>
                <line x1="4" y1="10" x2="4" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12" y2="3"></line>
                <line x1="20" y1="21" x2="20" y2="16"></line>
                <line x1="20" y1="12" x2="20" y2="3"></line>
                <line x1="1" y1="14" x2="7" y2="14"></line>
                <line x1="9" y1="8" x2="15" y2="8"></line>
                <line x1="17" y1="16" x2="23" y2="16"></line>
            </svg>
            <span>Filters</span>
        </button>

        <div class="catalog-results-count">
            Showing <?= count($products) ?> of <?= $totalProducts ?> pieces
        </div>

        <div class="catalog-sort-wrap">
            <label for="catalogSortSelect" class="sort-label">Sort By:</label>
            <select id="catalogSortSelect" class="sort-select" onchange="location = this.value;">
                <?php
                $sortOptions = [
                    'newest' => 'Newest First',
                    'price_low' => 'Price: Low to High',
                    'price_high' => 'Price: High to Low',
                    'popular' => 'Most Coveted',
                    'rating' => 'Highest Rated'
                ];
                foreach ($sortOptions as $key => $label):
                    $queryParams = $_GET;
                    $queryParams['sort'] = $key;
                    $url = '?' . http_build_query($queryParams);
                ?>
                    <option value="<?= e($url) ?>" <?= $sortParam === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="shop-layout">
        <!-- Sidebar Filters (Desktop & Mobile Drawer) -->
        <aside class="shop-sidebar-filters" id="shopSidebarFilters">
            <div class="filters-sidebar-inner">
                <div class="filters-drawer-header">
                    <h3>Refine Catalog</h3>
                    <button type="button" class="filters-close-btn" id="closeMobileFilterBtn">&times;</button>
                </div>

                <form action="<?= BASE_URL ?>/shop.php" method="GET" id="catalogFilterForm">
                    <?php if (!empty($searchQuery)): ?>
                        <input type="hidden" name="search" value="<?= e($searchQuery) ?>">
                    <?php endif; ?>
                    <?php if (!empty($sortParam)): ?>
                        <input type="hidden" name="sort" value="<?= e($sortParam) ?>">
                    <?php endif; ?>

                    <!-- Filter 1: Categories -->
                    <div class="filter-group">
                        <h4 class="filter-group-title">Category</h4>
                        <div class="filter-options-list">
                            <label class="filter-radio-label">
                                <input type="radio" name="category" value="" <?= empty($categorySlug) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <span>All Silhouettes</span>
                            </label>
                            <?php foreach ($categories as $cat): ?>
                                <label class="filter-radio-label">
                                    <input type="radio" name="category" value="<?= e($cat['slug']) ?>" <?= $categorySlug === $cat['slug'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <span><?= e($cat['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Filter 2: Price Range -->
                    <div class="filter-group">
                        <h4 class="filter-group-title">Price Range</h4>
                        <div class="price-input-row">
                            <input type="number" name="min_price" class="price-input" placeholder="Min (₹)" value="<?= $minPrice > 0 ? $minPrice : '' ?>">
                            <span class="price-sep">–</span>
                            <input type="number" name="max_price" class="price-input" placeholder="Max (₹)" value="<?= $maxPrice > 0 ? $maxPrice : '' ?>">
                        </div>
                        <button type="submit" class="btn btn-outline btn-sm filter-apply-btn">Apply Price</button>
                    </div>

                    <!-- Filter 3: Sizes -->
                    <div class="filter-group">
                        <h4 class="filter-group-title">Sizes</h4>
                        <div class="size-swatches-grid">
                            <?php
                            $availableSizes = ['XS', 'S', 'M', 'L', 'XL', 'Free Size'];
                            foreach ($availableSizes as $sz):
                            ?>
                                <label class="size-swatch-item">
                                    <input type="radio" name="size" value="<?= e($sz) ?>" <?= $filterSize === $sz ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <span class="size-box"><?= e($sz) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Filter 4: Colors -->
                    <div class="filter-group">
                        <h4 class="filter-group-title">Color Palette</h4>
                        <div class="color-options-list">
                            <?php
                            $colors = ['Ivory', 'Gold', 'Black', 'Navy', 'Burgundy', 'Emerald', 'Rose Gold'];
                            foreach ($colors as $clr):
                            ?>
                                <label class="filter-radio-label">
                                    <input type="radio" name="color" value="<?= e($clr) ?>" <?= $filterColor === $clr ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <span><?= e($clr) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <div class="filter-actions">
                        <a href="<?= BASE_URL ?>/shop.php" class="btn btn-text reset-filters-link">Clear All Filters</a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Product Grid Area -->
        <main class="shop-grid-area">
            <?php if (!empty($products)): ?>
                <div class="products-editorial-grid shop-catalog-grid">
                    <?php foreach ($products as $product): ?>
                        <?php include __DIR__ . '/includes/product-card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="catalog-pagination" aria-label="Catalog Pages">
                        <?php if ($page > 1): ?>
                            <?php $prevParams = $_GET; $prevParams['page'] = $page - 1; ?>
                            <a href="?<?= http_build_query($prevParams) ?>" class="page-link prev-link">&larr; Previous</a>
                        <?php endif; ?>

                        <div class="page-numbers">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php $numParams = $_GET; $numParams['page'] = $i; ?>
                                <a href="?<?= http_build_query($numParams) ?>" class="page-link <?= $i === $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <?php $nextParams = $_GET; $nextParams['page'] = $page + 1; ?>
                            <a href="?<?= http_build_query($nextParams) ?>" class="page-link next-link">Next &rarr;</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <!-- Empty Results State -->
                <div class="catalog-empty-state">
                    <span class="empty-icon">❖</span>
                    <h3 class="empty-title">No Silhouettes Found</h3>
                    <p class="empty-lead">We could not find any garments matching your current filter criteria.</p>
                    <a href="<?= BASE_URL ?>/shop.php" class="btn btn-outline">Explore All Collections</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<div class="mobile-filter-backdrop" id="mobileFilterBackdrop"></div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
