<?php
session_start();
require_once 'config.php';

// Require authentication
requireAuth();

$pageTitle = 'Checkout';
include 'includes/header.php';

$userId = getUserId();
$response = makeApiRequest('/cart/' . $userId, 'GET', null, true);
$cart = $response['data'] ?? null;

if (!$cart || empty($cart['items'])) {
    header('Location: cart.php');
    exit;
}
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Checkout</h2>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Shipping Information</h5>

                    <form id="checkoutForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="firstName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="lastName" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" id="phone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Street Address *</label>
                            <input type="text" class="form-control" id="address" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="postalCode">
                            </div>
                        </div>

                        <h5 class="card-title mb-3 mt-4">Payment Method</h5>

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="paymentMethod"
                                       id="paystack" value="Card" checked>
                                <label class="form-check-label" for="paystack">
                                    <i class="fas fa-credit-card me-2"></i>Card Payment
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod"
                                       id="mpesa" value="M-Pesa">
                                <label class="form-check-label" for="mpesa">
                                    <i class="fas fa-mobile-alt me-2"></i>M-Pesa
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-lock me-2"></i>Complete Order
                        </button>
                    </form>

                    <div id="message" class="alert mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Summary</h5>

                    <?php foreach ($cart['items'] as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">
                                <?php echo htmlspecialchars($item['productName']); ?> x <?php echo $item['quantity']; ?>
                            </span>
                            <span><?php echo formatCurrency($item['subtotal']); ?></span>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span><?php echo formatCurrency($cart['totalAmount']); ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping:</span>
                        <span class="text-success">Free</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total:</strong>
                        <strong class="text-primary">
                            <?php echo formatCurrency($cart['totalAmount']); ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const messageDiv = document.getElementById('message');
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

    const shippingAddress = `${document.getElementById('address').value}, ${document.getElementById('city').value}, ${document.getElementById('postalCode').value || ''}`;
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;

    const checkoutData = {
        userId: <?php echo getUserId(); ?>,
        shippingAddress: shippingAddress,
        paymentMethod: paymentMethod
    };

    let orderId, orderNumber, paymentReference;

    fetch('ajax/checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(checkoutData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store order ID for later verification
            orderId = data.orderId;
            orderNumber = data.orderNumber;
            sessionStorage.setItem('pendingOrderId', orderId);

            // Create payment intent
            const amount = <?php echo $cart['totalAmount'] * 100; ?>; // Convert to cents
            const callbackUrl = window.location.origin + window.location.pathname.replace('checkout.php', 'payment_callback.php');

            console.log('Payment Intent Details:');
            console.log('- Amount:', amount);
            console.log('- Callback URL:', callbackUrl);
            console.log('- Order Number:', orderNumber);

            return fetch('ajax/create_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: amount,
                    currency: 'KES',
                    description: 'Order #' + orderNumber,
                    email: document.getElementById('email').value,
                    callbackUrl: callbackUrl
                })
            });
        } else {
            throw new Error(data.message || 'Failed to create order');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            paymentReference = data.reference;

            // Check if we have an authorization URL (Paystack redirect flow)
            if (data.authorizationUrl) {
                // Redirect to Paystack payment page
                // Verification and confirmation will happen in payment_callback.php
                window.location.href = data.authorizationUrl;
            } else {
                // For non-redirect flows (e.g., MPESA STK Push)
                // Complete the flow here: verify → confirm
                return verifyAndConfirmPayment(orderId, paymentReference);
            }
        } else {
            throw new Error(data.message || 'Failed to initialize payment');
        }
    })
    .catch(error => {
        messageDiv.className = 'alert alert-danger';
        messageDiv.textContent = error.message;
        messageDiv.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Complete Order';
    });
});

function verifyAndConfirmPayment(orderId, reference) {
    const messageDiv = document.getElementById('message');

    // Step 1: Verify payment
    return fetch(`ajax/verify_payment.php?reference=${reference}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data === true) {
                // Step 2: Confirm payment and update order
                return fetch('ajax/confirm_payment.php', {
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

                // Show success message and redirect
                messageDiv.className = 'alert alert-success';
                messageDiv.textContent = 'Payment successful! Redirecting to your orders...';
                messageDiv.style.display = 'block';

                setTimeout(() => {
                    window.location.href = 'orders.php';
                }, 2000);
            } else {
                throw new Error(data.message || 'Failed to confirm payment');
            }
        })
        .catch(error => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = error.message;
            messageDiv.style.display = 'block';

            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Complete Order';

            throw error;
        });
}
</script>

<?php include 'includes/footer.php'; ?>