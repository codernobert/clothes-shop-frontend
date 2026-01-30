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

<!-- Portfolio Demo Section for Interviewers -->
<div class="bg-light py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <p class="text-muted mb-4">
                    Explore both the <strong>customer experience</strong> and the <strong>admin dashboard</strong> to see the complete application in action.
                </p>
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-lock me-2"></i>Admin Dashboard Demo Credentials
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Email:</strong>
                            <code class="bg-light p-2 rounded">admin@clothesshop.com</code>
                        </p>
                        <p class="mb-3">
                            <strong>Password:</strong>
                            <code class="bg-light p-2 rounded">password123</code>
                        </p>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle me-1"></i>Use these credentials to access the admin panel and explore product management, order management, and dashboard features.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x text-success mb-3"></i>
                                <h5>Customer Experience</h5>
                                <p class="text-muted small">Browse products, manage cart, and place orders</p>
                                <a href="products.php" class="btn btn-success btn-sm">
                                    <i class="fas fa-shopping-bag me-1"></i>Shop
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-user-tie fa-2x text-info mb-3"></i>
                                <h5>Admin Dashboard</h5>
                                <p class="text-muted small">Manage products, orders, and view analytics</p>
                                <a href="login.php?redirect=admin/index.php" class="btn btn-info btn-sm">
                                    <i class="fas fa-sign-in-alt me-1"></i>Admin Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mt-4 mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Pro Tip:</strong> Login with the demo admin credentials above, then navigate to the admin dashboard to see advanced features!
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Categories Section -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center">Shop by Category</h2>
        </div>
        <div class="col-md-3 mb-3">
            <a href="products.php?category=TOPS" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm">
                    <i class="fas fa-tshirt fa-3x text-primary mb-3"></i>
                    <h5>Tops</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="products.php?category=BOTTOMS" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm">
                    <i class="fas fa-socks fa-3x text-success mb-3"></i>
                    <h5>Bottoms</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="products.php?category=DRESSES" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm">
                    <i class="fas fa-vest fa-3x text-danger mb-3"></i>
                    <h5>Dresses</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="products.php?category=OUTERWEAR" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm">
                    <i class="fas fa-coat-hanger fa-3x text-warning mb-3"></i>
                    <h5>Outerwear</h5>
                </div>
            </a>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center">Featured Products</h2>
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
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>No products available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="text-center p-4">
                <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h5>Fast Delivery</h5>
                <p class="text-muted">Quick and reliable shipping to your doorstep</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="text-center p-4">
                <i class="fas fa-lock fa-3x text-success mb-3"></i>
                <h5>Secure Payment</h5>
                <p class="text-muted">Multiple payment options with secure transactions</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="text-center p-4">
                <i class="fas fa-undo fa-3x text-warning mb-3"></i>
                <h5>Easy Returns</h5>
                <p class="text-muted">Hassle-free returns within 30 days</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>