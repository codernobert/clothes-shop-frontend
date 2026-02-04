<?php
session_start();
require_once 'config.php';

// Require authentication
requireAuth();

$pageTitle = 'Shopping Cart';
include 'includes/header.php';

$userId = getUserId();
$response = makeApiRequest('/cart/' . $userId, 'GET', null, true);
$cart = $response['data'] ?? null;
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h2>

    <?php if ($cart && !empty($cart['items'])): ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <?php foreach ($cart['items'] as $item): ?>
                            <div class="row mb-3 pb-3 border-bottom" data-item-id="<?php echo $item['id']; ?>">
                                <div class="col-md-8">
                                    <h5><?php echo htmlspecialchars($item['productName']); ?></h5>
                                    <p class="text-muted mb-2">
                                        Price: <?php echo formatCurrency($item['price']); ?>
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <label class="me-2">Quantity:</label>
                                        <input type="number" class="form-control form-control-sm quantity-input"
                                               style="width: 80px;"
                                               value="<?php echo $item['quantity']; ?>"
                                               min="1"
                                               data-item-id="<?php echo $item['id']; ?>">
                                        <button class="btn btn-sm btn-outline-danger ms-3 remove-item"
                                                data-item-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h5 class="text-primary">
                                        <?php echo formatCurrency($item['subtotal']); ?>
                                    </h5>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Order Summary</h5>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span id="subtotal"><?php echo formatCurrency($cart['totalAmount']); ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span class="text-success">Free</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong class="text-primary" id="total">
                                <?php echo formatCurrency($cart['totalAmount']); ?>
                            </strong>
                        </div>

                        <a href="checkout.php" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-lock me-2"></i>Proceed to Checkout
                        </a>

                        <a href="products.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted mb-4">Add some products to get started!</p>
            <a href="products.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-2"></i>Browse Products
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
const userId = <?php echo getUserId(); ?>;

// Update quantity
document.querySelectorAll('.quantity-input').forEach(input => {
    let timeout;
    input.addEventListener('change', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            updateCartItem(this.dataset.itemId, this.value);
        }, 500);
    });
});

// Remove item
document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', function() {
        if (confirm('Are you sure you want to remove this item?')) {
            removeCartItem(this.dataset.itemId);
        }
    });
});

function updateCartItem(itemId, quantity) {
    fetch(`ajax/update_cart.php?userId=${userId}&itemId=${itemId}&quantity=${quantity}`, {
        method: 'PUT'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update cart');
        }
    });
}

function removeCartItem(itemId) {
    fetch(`ajax/remove_from_cart.php?userId=${userId}&itemId=${itemId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to remove item');
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>