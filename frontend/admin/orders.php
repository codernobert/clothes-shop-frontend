<?php
session_start();
require_once '../config.php';
requireAdminAuth();

$pageTitle = 'Manage Orders';
include '../includes/header.php';

$orders = makeApiRequest('/admin/orders');
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Manage Orders</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['orderNumber']); ?></td>
                                    <td>User #<?php echo $order['userId']; ?></td>
                                    <td><?php echo formatCurrency($order['totalAmount']); ?></td>
                                    <td>
                                        <select class="form-select form-select-sm status-select"
                                                data-order-id="<?php echo $order['id']; ?>"
                                                style="width: 150px;">
                                            <option value="PENDING" <?php echo $order['status'] === 'PENDING' ? 'selected' : ''; ?>>
                                                Pending
                                            </option>
                                            <option value="CONFIRMED" <?php echo $order['status'] === 'CONFIRMED' ? 'selected' : ''; ?>>
                                                Confirmed
                                            </option>
                                            <option value="PROCESSING" <?php echo $order['status'] === 'PROCESSING' ? 'selected' : ''; ?>>
                                                Processing
                                            </option>
                                            <option value="SHIPPED" <?php echo $order['status'] === 'SHIPPED' ? 'selected' : ''; ?>>
                                                Shipped
                                            </option>
                                            <option value="DELIVERED" <?php echo $order['status'] === 'DELIVERED' ? 'selected' : ''; ?>>
                                                Delivered
                                            </option>
                                            <option value="CANCELLED" <?php echo $order['status'] === 'CANCELLED' ? 'selected' : ''; ?>>
                                                Cancelled
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php
                                            echo $order['paymentStatus'] === 'COMPLETED' ? 'success' : 'warning';
                                        ?>">
                                            <?php echo htmlspecialchars($order['paymentStatus']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order_detail.php?id=<?php echo $order['id']; ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const orderId = this.dataset.orderId;
        const newStatus = this.value;

        if (confirm(`Update order status to ${newStatus}?`)) {
            fetch(`../ajax/admin/update_order_status.php?orderId=${orderId}&status=${newStatus}`, {
                method: 'PATCH'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order status updated successfully');
                    location.reload();
                } else {
                    alert('Failed to update order status');
                    location.reload();
                }
            })
            .catch(error => {
                alert('An error occurred');
                location.reload();
            });
        } else {
            location.reload();
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>