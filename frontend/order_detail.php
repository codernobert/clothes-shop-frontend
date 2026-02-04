<?php
session_start();
require_once 'config.php';

// Require authentication
requireAuth();

$pageTitle = 'Order Details';
include 'includes/header.php';

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

// Get order ID from query parameter
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$orderId) {
    echo '<div class="container mt-4"><div class="alert alert-danger">Invalid order ID</div></div>';
    include 'includes/footer.php';
    exit;
}

// Fetch order details
$apiResponse = makeApiRequest('/orders/' . $orderId, 'GET', null, true);

// Extract data from wrapped response (API returns {data: {...}, success: true})
$order = null;
if ($apiResponse && is_array($apiResponse)) {
    // If response has a 'data' wrapper
    if (isset($apiResponse['data']) && is_array($apiResponse['data'])) {
        $order = $apiResponse['data'];
    }
    // Otherwise assume direct response
    elseif (!isset($apiResponse['success'])) {
        $order = $apiResponse;
    }
}

// Check if order exists and has required fields
if (!$order || !is_array($order) || empty($order['id']) || isset($order['error'])) {
    echo '<div class="container mt-4">';
    echo '<div class="alert alert-danger"><strong>Error:</strong> Order not found or API error occurred</div>';
    if ($apiResponse && isset($apiResponse['message'])) {
        echo '<p class="text-muted">Error: ' . htmlspecialchars($apiResponse['message']) . '</p>';
    } elseif ($order && isset($order['error'])) {
        echo '<p class="text-muted">Error: ' . htmlspecialchars($order['error']) . '</p>';
    }
    echo '<a href="orders.php" class="btn btn-secondary">Back to Orders</a>';
    echo '</div></div>';
    include 'includes/footer.php';
    exit;
}

// Verify user owns this order (security check)
$userId = getUserId();
if (isset($order['userId']) && $order['userId'] !== $userId && !isAdmin()) {
    echo '<div class="container mt-4"><div class="alert alert-danger">You do not have permission to view this order</div></div>';
    include 'includes/footer.php';
    exit;
}
?>

<div class="container mt-4">
    <div class="mb-4">
        <a href="orders.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
    </div>

    <div class="row">
        <!-- Order Header -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="mb-0">Order #<?php echo htmlspecialchars($order['orderNumber'] ?? 'N/A'); ?></h3>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-<?php
                                echo match($order['status'] ?? 'unknown') {
                                    'PENDING' => 'warning',
                                    'CONFIRMED' => 'info',
                                    'PROCESSING' => 'primary',
                                    'SHIPPED' => 'success',
                                    'DELIVERED' => 'success',
                                    'CANCELLED' => 'danger',
                                    default => 'secondary'
                                };
                            ?>" style="font-size: 14px; padding: 8px 12px;">
                                <?php echo htmlspecialchars($order['status'] ?? 'Unknown'); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Order Date</h6>
                            <p class="mb-0"><?php echo date('F j, Y', strtotime($order['createdAt'] ?? date('Y-m-d'))); ?></p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Payment Status</h6>
                            <p class="mb-0">
                                <span class="badge bg-<?php
                                    echo ($order['paymentStatus'] ?? 'PENDING') === 'COMPLETED' ? 'success' : 'warning';
                                ?>">
                                    <?php echo htmlspecialchars($order['paymentStatus'] ?? 'PENDING'); ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Total Amount</h6>
                            <p class="mb-0 fs-5"><strong class="text-primary"><?php echo formatCurrency($order['totalAmount'] ?? 0); ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($order['items'])): ?>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($item['productName'] ?? 'Unknown Product'); ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $item['quantity'] ?? 0; ?>
                                            </td>
                                            <td class="text-end">
                                                <?php echo formatCurrency($item['price'] ?? 0); ?>
                                            </td>
                                            <td class="text-end">
                                                <strong><?php echo formatCurrency($item['subtotal'] ?? 0); ?></strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No items in this order</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-6">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td><strong>Subtotal:</strong></td>
                                            <td class="text-end"><?php echo formatCurrency($order['totalAmount'] ?? 0); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Shipping:</strong></td>
                                            <td class="text-end"><?php echo formatCurrency(0); ?></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><h5 class="mb-0"><strong>Total:</strong></h5></td>
                                            <td class="text-end"><h5 class="mb-0 text-primary"><strong><?php echo formatCurrency($order['totalAmount'] ?? 0); ?></strong></h5></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        <?php echo htmlspecialchars($order['shippingAddress']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        <?php echo formatPaymentMethod($order['paymentMethod']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
