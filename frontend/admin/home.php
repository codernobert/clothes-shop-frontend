<?php
session_start();
require_once '../config.php';
requireAdminAuth();

$pageTitle = 'Admin Dashboard';
include '../includes/header.php';

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
            <p class="lead mb-4">Admin Dashboard</p>
        <?php else: ?>
            <h1 class="display-4 fw-bold mb-3">Welcome to Admin Panel</h1>
            <p class="lead mb-4">Manage your store</p>
        <?php endif; ?>
        <a href="products.php" class="btn btn-light btn-lg">
            <i class="fas fa-box me-2"></i>Manage Products
        </a>
    </div>
</div>

<div class="container">
    <!-- Admin Quick Links -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center">Admin Tools</h2>
        </div>
        <div class="col-md-3 mb-3">
            <a href="products.php" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm">
                    <i class="fas fa-box fa-3x text-primary mb-3"></i>
                    <h5>Manage Products</h5>
                    <p class="text-muted small">Add, edit, and delete products</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="orders.php" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm">
                    <i class="fas fa-receipt fa-3x text-success mb-3"></i>
                    <h5>View Orders</h5>
                    <p class="text-muted small">Manage customer orders</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="add_product.php" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm">
                    <i class="fas fa-plus-circle fa-3x text-info mb-3"></i>
                    <h5>Add Product</h5>
                    <p class="text-muted small">Create new products</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="index.php" class="text-decoration-none">
                <div class="card text-center p-4 h-100 border-0 shadow-sm">
                    <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                    <h5>Dashboard</h5>
                    <p class="text-muted small">View statistics</p>
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
                                <a href="../product_detail.php?id=<?php echo $product['id']; ?>"
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

    <!-- Admin Features Section -->
    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="text-center p-4">
                <i class="fas fa-cogs fa-3x text-primary mb-3"></i>
                <h5>Product Management</h5>
                <p class="text-muted">Full control over inventory</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="text-center p-4">
                <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                <h5>Order Tracking</h5>
                <p class="text-muted">Monitor all customer orders</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="text-center p-4">
                <i class="fas fa-lock-open fa-3x text-warning mb-3"></i>
                <h5>Admin Control</h5>
                <p class="text-muted">Complete store management</p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
