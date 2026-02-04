<?php
session_start();
$pageTitle = 'Payment Processing';
include 'includes/header.php';

// Get reference from URL
$reference = $_GET['reference'] ?? null;

if (!$reference) {
    header('Location: index.php');
    exit;
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm text-center">
                <div class="card-body p-5">
                    <div id="loading">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h4>Verifying Payment...</h4>
                        <p class="text-muted">Please wait while we confirm your payment.</p>
                    </div>

                    <div id="success" style="display: none;">
                        <i class="fas fa-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                        <h4 class="text-success">Payment Successful!</h4>
                        <p class="text-muted mb-4">Your order has been confirmed.</p>
                        <div id="orderDetails" class="mb-4"></div>
                        <a href="orders.php" class="btn btn-primary">View My Orders</a>
                        <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
                    </div>

                    <div id="failed" style="display: none;">
                        <i class="fas fa-times-circle text-danger mb-3" style="font-size: 4rem;"></i>
                        <h4 class="text-danger">Payment Failed</h4>
                        <p class="text-muted mb-4" id="errorMessage">There was an issue processing your payment.</p>
                        <a href="checkout.php" class="btn btn-primary">Try Again</a>
                        <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const reference = '<?php echo htmlspecialchars($reference); ?>';

// Get orderId from sessionStorage (set during checkout)
const orderId = sessionStorage.getItem('pendingOrderId');

if (!orderId) {
    showFailed('Order information not found. Please contact support.');
} else {
    verifyAndConfirmPayment();
}

function verifyAndConfirmPayment() {
    // Step 1: Verify payment with Paystack
    fetch(`ajax/verify_payment.php?reference=${reference}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data === true) {
                // Step 2: Confirm payment and update order
                return fetch(`ajax/confirm_payment.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        orderId: orderId,
                        reference: reference
                    })
                });
            } else {
                throw new Error('Payment verification failed');
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the pending order
                sessionStorage.removeItem('pendingOrderId');
                showSuccess(data.data);
            } else {
                throw new Error(data.message || 'Failed to confirm payment');
            }
        })
        .catch(error => {
            showFailed(error.message);
        });
}

function showSuccess(orderData) {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('failed').style.display = 'none';

    const orderDetails = document.getElementById('orderDetails');
    orderDetails.innerHTML = `
        <div class="alert alert-info">
            <strong>Order Number:</strong> ${orderData.orderNumber || 'N/A'}<br>
            <strong>Total Amount:</strong> KSh ${(orderData.totalAmount || 0).toFixed(2)}
        </div>
    `;

    document.getElementById('success').style.display = 'block';
}

function showFailed(message) {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('success').style.display = 'none';
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('failed').style.display = 'block';
}
</script>

<?php include 'includes/footer.php'; ?>
