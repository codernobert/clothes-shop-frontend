<?php
session_start();
require_once '../config.php';
requireAdminAuth();

$pageTitle = 'Admin Dashboard';
include '../includes/header.php';

// Fetch statistics
$products = makeApiRequest('/products');
$orders = makeApiRequest('/admin/orders');

$totalProducts = count($products ?? []);
$totalOrders = count($orders ?? []);
$pendingOrders = count(array_filter($orders ?? [], fn($o) => $o['status'] === 'PENDING'));
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Products</h6>
                            <h2 class="mb-0"><?php echo $totalProducts; ?></h2>
                        </div>
                        <i class="fas fa-box fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Orders</h6>
                            <h2 class="mb-0"><?php echo $totalOrders; ?></h2>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Pending Orders</h6>
                            <h2 class="mb-0"><?php echo $pendingOrders; ?></h2>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Revenue</h6>
                            <h2 class="mb-0">
                                <?php
                                $revenue = array_sum(array_map(fn($o) => $o['totalAmount'], $orders ?? []));
                                echo formatCurrency($revenue);
                                ?>
                            </h2>
                        </div>
                        <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Quick Actions</h5>
                    <a href="products.php" class="btn btn-primary me-2 mb-2">
                        <i class="fas fa-box me-2"></i>Manage Products
                    </a>
                    <a href="orders.php" class="btn btn-success me-2 mb-2">
                        <i class="fas fa-shopping-cart me-2"></i>Manage Orders
                    </a>
                    <a href="add_product.php" class="btn btn-info me-2 mb-2">
                        <i class="fas fa-plus me-2"></i>Add New Product
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recentOrders = array_slice($orders ?? [], 0, 10);
                                foreach ($recentOrders as $order):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['orderNumber']); ?></td>
                                        <td><?php echo formatCurrency($order['totalAmount']); ?></td>
                                        <td>
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
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>