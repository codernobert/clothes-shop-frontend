<?php
session_start();
require_once 'config.php';

$pageTitle = 'Products';
include 'includes/header.php';

// Get filter parameters
$category = $_GET['category'] ?? null;
$brand = $_GET['brand'] ?? null;
$minPrice = $_GET['minPrice'] ?? null;
$maxPrice = $_GET['maxPrice'] ?? null;
$gender = $_GET['gender'] ?? null;
$search = $_GET['search'] ?? null;

// Build API endpoint
if ($search) {
    $endpoint = '/products/search?keyword=' . urlencode($search);
    $products = makeApiRequest($endpoint);
} elseif ($category || $brand || $minPrice || $maxPrice || $gender) {
    $params = [];
    if ($category) $params[] = 'category=' . urlencode($category);
    if ($brand) $params[] = 'brand=' . urlencode($brand);
    if ($minPrice) $params[] = 'minPrice=' . $minPrice;
    if ($maxPrice) $params[] = 'maxPrice=' . $maxPrice;
    if ($gender) $params[] = 'gender=' . urlencode($gender);

    $endpoint = '/products/filter?' . implode('&', $params);
    $products = makeApiRequest($endpoint);
} else {
    $products = makeApiRequest('/products');
}
?>

<div class="container mt-4">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="filter-section">
                <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filters</h5>

                <form method="GET" action="products.php">
                    <!-- Search -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Search</label>
                        <input type="text" class="form-control" name="search"
                               value="<?php echo htmlspecialchars($search ?? ''); ?>"
                               placeholder="Search products...">
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <option value="TOPS" <?php echo $category === 'TOPS' ? 'selected' : ''; ?>>Tops</option>
                            <option value="BOTTOMS" <?php echo $category === 'BOTTOMS' ? 'selected' : ''; ?>>Bottoms</option>
                            <option value="DRESSES" <?php echo $category === 'DRESSES' ? 'selected' : ''; ?>>Dresses</option>
                            <option value="OUTERWEAR" <?php echo $category === 'OUTERWEAR' ? 'selected' : ''; ?>>Outerwear</option>
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Gender</label>
                        <select class="form-select" name="gender">
                            <option value="">All</option>
                            <option value="MEN" <?php echo $gender === 'MEN' ? 'selected' : ''; ?>>Men</option>
                            <option value="WOMEN" <?php echo $gender === 'WOMEN' ? 'selected' : ''; ?>>Women</option>
                            <option value="UNISEX" <?php echo $gender === 'UNISEX' ? 'selected' : ''; ?>>Unisex</option>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Price Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control" name="minPrice"
                                       value="<?php echo htmlspecialchars($minPrice ?? ''); ?>"
                                       placeholder="Min">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="maxPrice"
                                       value="<?php echo htmlspecialchars($maxPrice ?? ''); ?>"
                                       placeholder="Max">
                            </div>
                        </div>
                    </div>

                    <!-- Brand Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Brand</label>
                        <input type="text" class="form-control" name="brand"
                               value="<?php echo htmlspecialchars($brand ?? ''); ?>"
                               placeholder="Brand name">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                    <a href="products.php" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </a>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>
                    <?php
                    if ($search) {
                        echo 'Search Results for "' . htmlspecialchars($search) . '"';
                    } elseif ($category) {
                        echo htmlspecialchars($category);
                    } else {
                        echo 'All Products';
                    }
                    ?>
                </h3>
                <span class="text-muted"><?php echo count($products ?? []); ?> products found</span>
            </div>

            <div class="row">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card product-card h-100 shadow-sm">
                                <div class="product-image d-flex align-items-center justify-content-center text-white">
                                    <?php if ($product['imageUrl']): ?>
                                        <img src="<?php echo htmlspecialchars($product['imageUrl']); ?>"
                                             class="card-img-top w-100 h-100"
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <?php else: ?>
                                        <i class="fas fa-tshirt fa-5x"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="category-badge"><?php echo htmlspecialchars($product['category']); ?></span>
                                        <?php if ($product['stockQuantity'] > 0): ?>
                                            <small class="text-success"><i class="fas fa-check-circle"></i> In Stock</small>
                                        <?php else: ?>
                                            <small class="text-danger"><i class="fas fa-times-circle"></i> Out of Stock</small>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-muted small">
                                        <?php echo htmlspecialchars($product['brand'] ?? 'No brand'); ?> •
                                        <?php echo htmlspecialchars($product['size'] ?? 'One size'); ?>
                                    </p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="price-tag"><?php echo formatCurrency($product['price']); ?></span>
                                            <a href="product_detail.php?id=<?php echo $product['id']; ?>"
                                               class="btn btn-primary btn-sm">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            No products found matching your criteria.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>