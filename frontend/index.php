<?php
session_start();
require_once 'config.php';

$pageTitle = 'Home';
include 'includes/header.php';

// Fetch featured products
$products = makeApiRequest('/products');
$featuredProducts = array_slice($products ?? [], 0, 8);

// Get current user if logged in
$user = getCurrentUser();
?>

<style>
    .interactive-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .interactive-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    }
</style>

<div class="hero-section text-center">
    <div class="container">
        <?php if ($user): ?>
            <h1 class="display-4 fw-bold mb-3">Welcome back, <?php echo htmlspecialchars($user['firstName']); ?>! 👋</h1>
            <p class="lead mb-4">Discover the latest trends in fashion</p>
        <?php else: ?>
            <h1 class="display-4 fw-bold mb-3">Welcome to Clothes Shop</h1>
            <p class="lead mb-4">Discover the latest trends in fashion</p>
        <?php endif; ?>
        <a href="products.php" class="btn btn-light btn-lg">
            <i class="fas fa-shopping-bag me-2"></i>Shop Now
        </a>
    </div>
</div>

<div class="container">
    <!-- Quick Links Section -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center fw-bold"><i class="fas fa-bolt text-warning me-2"></i>Quick Links</h2>
        </div>
        <div class="col-md-4 mb-3">
            <a href="products.php" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm interactive-card">
                    <i class="fas fa-shopping-bag fa-3x text-primary mb-3"></i>
                    <h5>Browse All Products</h5>
                    <p class="text-muted small">Explore our full collection</p>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="cart.php" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm interactive-card">
                    <i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
                    <h5>My Cart</h5>
                    <p class="text-muted small">Review your items</p>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="orders.php" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm interactive-card">
                    <i class="fas fa-receipt fa-3x text-info mb-3"></i>
                    <h5>My Orders</h5>
                    <p class="text-muted small">Track your purchases</p>
                </div>
            </a>
        </div>
    </div>


    <!-- Categories Section -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center fw-bold"><i class="fas fa-th-large text-info me-2"></i>Shop by Category</h2>
        </div>
        <div class="col-md-3 mb-3">
            <a href="products.php?category=TOPS" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm interactive-card">
                    <i class="fas fa-tshirt fa-3x text-primary mb-3"></i>
                    <h5>Tops</h5>
                    <p class="text-muted small">T-shirts, Shirts & More</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="products.php?category=BOTTOMS" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm interactive-card">
                    <i class="fas fa-shoe-prints fa-3x text-success mb-3"></i>
                    <h5>Bottoms</h5>
                    <p class="text-muted small">Jeans, Shorts & Pants</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="products.php?category=DRESSES" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm interactive-card">
                    <i class="fas fa-person-dress fa-3x text-danger mb-3"></i>
                    <h5>Dresses</h5>
                    <p class="text-muted small">Casual & Formal</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="products.php?category=OUTERWEAR" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm interactive-card">
                    <i class="fas fa-wind fa-3x text-warning mb-3"></i>
                    <h5>Outerwear</h5>
                    <p class="text-muted small">Jackets & Coats</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center fw-bold"><i class="fas fa-star text-warning me-2"></i>Featured Products</h2>
        </div>
        <?php if (!empty($featuredProducts)): ?>
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
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
                            <span class="category-badge mb-2"><?php echo htmlspecialchars($product['category']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 80)) . '...'; ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="price-tag"><?php echo formatCurrency($product['price']); ?></span>
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>"
                                   class="btn btn-primary btn-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center shadow-sm">
                    <i class="fas fa-info-circle me-2"></i>No products available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center fw-bold"><i class="fas fa-gift text-primary me-2"></i>Why Shop With Us</h2>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center p-4 border-0 shadow-sm h-100">
                <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h5>Fast Delivery</h5>
                <p class="text-muted">Quick and reliable shipping to your doorstep</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center p-4 border-0 shadow-sm h-100">
                <i class="fas fa-lock fa-3x text-success mb-3"></i>
                <h5>Secure Payment</h5>
                <p class="text-muted">Multiple payment options with secure transactions</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center p-4 border-0 shadow-sm h-100">
                <i class="fas fa-undo fa-3x text-warning mb-3"></i>
                <h5>Easy Returns</h5>
                <p class="text-muted">Hassle-free returns within 30 days</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>