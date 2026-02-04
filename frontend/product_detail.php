<?php
session_start();
require_once 'config.php';

// Check and fetch product BEFORE including header
$productId = $_GET['id'] ?? null;

if (!$productId) {
    header('Location: products.php');
    exit;
}

$response = makeApiRequest('/products/' . $productId);
$product = $response['data'] ?? null;

if (!$product) {
    header('Location: products.php');
    exit;
}

// Set page title and NOW include header
$pageTitle = $product['name'];
include 'includes/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php">Products</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Image -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div style="height: 500px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
                     class="d-flex align-items-center justify-content-center text-white">
                    <?php if ($product['imageUrl']): ?>
                        <img src="<?php echo htmlspecialchars($product['imageUrl']); ?>"
                             class="img-fluid"
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <i class="fas fa-tshirt" style="font-size: 150px;"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4">
                <span class="category-badge mb-3 d-inline-block">
                    <?php echo htmlspecialchars($product['category']); ?>
                </span>

                <h2 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h2>

                <div class="mb-4">
                    <span class="price-tag" style="font-size: 2rem;">
                        <?php echo formatCurrency($product['price']); ?>
                    </span>
                </div>

                <div class="mb-4">
                    <h5>Description</h5>
                    <p class="text-muted">
                        <?php echo htmlspecialchars($product['description'] ?? 'No description available'); ?>
                    </p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Brand</h6>
                        <p><?php echo htmlspecialchars($product['brand'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Size</h6>
                        <p><?php echo htmlspecialchars($product['size'] ?? 'One Size'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Color</h6>
                        <p><?php echo htmlspecialchars($product['color'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Gender</h6>
                        <p><?php echo htmlspecialchars($product['gender'] ?? 'Unisex'); ?></p>
                    </div>
                </div>

                <div class="mb-4">
                    <h6>Availability</h6>
                    <?php if ($product['stockQuantity'] > 0): ?>
                        <p class="text-success">
                            <i class="fas fa-check-circle me-2"></i>
                            In Stock (<?php echo $product['stockQuantity']; ?> available)
                        </p>
                    <?php else: ?>
                        <p class="text-danger">
                            <i class="fas fa-times-circle me-2"></i>Out of Stock
                        </p>
                    <?php endif; ?>
                </div>

                <form id="addToCartForm" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity"
                                   name="quantity" value="1" min="1"
                                   max="<?php echo $product['stockQuantity']; ?>"
                                   <?php echo $product['stockQuantity'] <= 0 ? 'disabled' : ''; ?>>
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100"
                                    <?php echo $product['stockQuantity'] <= 0 ? 'disabled' : ''; ?>>
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </form>

                <div id="message" class="alert" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const quantity = document.getElementById('quantity').value;
    const productId = <?php echo $productId; ?>;
    const userId = <?php echo getUserId() ?? 'null'; ?>;

    const messageDiv = document.getElementById('message');

    // Check if user is logged in
    if (!userId) {
        // Not logged in - redirect to login page
        window.location.href = 'login.php?redirect=product_detail.php?id=<?php echo $productId; ?>';
        return;
    }

    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            userId: userId,
            productId: productId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        // Check if authentication is required
        if (data.redirect) {
            window.location.href = data.redirect;
            return;
        }

        if (data.success) {
            messageDiv.className = 'alert alert-success';
            messageDiv.textContent = 'Product added to cart successfully!';
            messageDiv.style.display = 'block';
            updateCartCount();

            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 3000);
        } else {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = data.message || 'Failed to add product to cart';
            messageDiv.style.display = 'block';
        }
    })
    .catch(error => {
        messageDiv.className = 'alert alert-danger';
        messageDiv.textContent = 'An error occurred. Please try again.';
        messageDiv.style.display = 'block';
    });
});
</script>

<?php include 'includes/footer.php'; ?>