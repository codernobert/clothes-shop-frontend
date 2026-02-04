<?php
session_start();
require_once 'config.php';

// Require authentication
requireAuth();

$pageTitle = 'My Orders';
include 'includes/header.php';

$userId = getUserId();
$orders = makeApiRequest('/orders/user/' . $userId, 'GET', null, true);

/**
 * Format payment method for display
 * Handles both old values (PAYSTACK, MPESA) and new values (Card, M-Pesa)
 */
function formatPaymentMethod($method) {
    $methodMap = [
        'PAYSTACK' => 'Card',
        'Card' => 'Card',
        'MPESA' => 'M-Pesa',
        'M-Pesa' => 'M-Pesa'
    ];

    return $methodMap[$method] ?? htmlspecialchars($method);
}
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-box me-2"></i>My Orders</h2>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <strong>Order #<?php echo htmlspecialchars($order['orderNumber']); ?></strong>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-<?php
                                echo match($order['status']) {
                                    'PENDING' => 'warning',
                                    'CONFIRMED' => 'info',
                                    'PROCESSING' => 'primary',
                                    'SHIPPED' => 'success',
                                    'DELIVERED' => 'success',
                                    'CANCELLED' => 'danger',
                                    default => 'secondary'
                                };
                            ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </div>
                        <div class="col-md-3">
                            Payment:
                            <span class="badge bg-<?php
                                echo $order['paymentStatus'] === 'COMPLETED' ? 'success' : 'warning';
                            ?>">
                                <?php echo htmlspecialchars($order['paymentStatus']); ?>
                            </span>
                        </div>
                        <div class="col-md-3 text-end">
                            <strong class="text-primary">
                                <?php echo formatCurrency($order['totalAmount']); ?>
                            </strong>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Shipping Address:</h6>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($order['shippingAddress']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Method:</h6>
                            <p class="text-muted mb-0"><?php echo formatPaymentMethod($order['paymentMethod']); ?></p>
                        </div>
                    </div>

                    <h6>Items:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['productName']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo formatCurrency($item['price']); ?></td>
                                        <td class="text-end"><?php echo formatCurrency($item['subtotal']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-3">
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-5x text-muted mb-4"></i>
            <h3>No orders yet</h3>
            <p class="text-muted mb-4">Start shopping to see your orders here!</p>
            <a href="products.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-2"></i>Browse Products
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>