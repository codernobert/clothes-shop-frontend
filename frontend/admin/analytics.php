<?php
session_start();
require_once '../config.php';
requireAdminAuth();

$pageTitle = 'Analytics Dashboard';
include '../includes/header.php';

// Get JWT token from session
$token = $_SESSION['access_token'] ?? null;

if (!$token) {
    header('Location: login.php?redirect=analytics.php');
    exit;
}

// Get current section from query string or default to dashboard
$currentSection = $_GET['section'] ?? 'dashboard';

// Fetch analytics data from backend
function getAnalyticsData($endpoint) {
    global $token;

    $url = API_BASE_URL . '/admin/analytics/' . $endpoint;

    $headers = "Content-Type: application/json\r\n";
    $headers .= "Authorization: Bearer $token\r\n";

    $options = [
        'http' => [
            'method' => 'GET',
            'header' => $headers,
            'ignore_errors' => true,
            'timeout' => 30
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return null;
    }

    return json_decode($response, true);
}

// Fetch all analytics data
$dashboard = getAnalyticsData('dashboard');
$revenue = getAnalyticsData('revenue?period=daily');
$orders = getAnalyticsData('orders');
$products = getAnalyticsData('products');
$customers = getAnalyticsData('customers');
$payments = getAnalyticsData('payments');

// Extract data with fallback values
$dashboardData = $dashboard['data'] ?? [];
$revenueData = $revenue['data'] ?? [];
$ordersData = $orders['data'] ?? [];
$productsData = $products['data'] ?? [];
$customersData = $customers['data'] ?? [];
$paymentsData = $payments['data'] ?? [];
?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --primary-color: #667eea;
        --secondary-color: #764ba2;
        --navbar-height: 60px;
    }

    /* Ensure body and html take full height */
    html, body {
        height: 100%;
        overflow-x: hidden;
    }

    .analytics-wrapper {
        display: flex;
        min-height: 100vh;
        background: #f8f9fa;
        position: relative;
        margin-left: 260px;
    }

    /* Sidebar Navigation - Truly Sticky */
    .analytics-sidebar {
        width: 260px;
        background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
        padding: 30px 0;
        position: fixed;
        left: 0;
        top: var(--navbar-height);
        height: calc(100vh - var(--navbar-height));
        overflow-y: auto;
        overflow-x: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        z-index: 99;
        display: flex;
        flex-direction: column;
        /* Smooth scrolling */
        scroll-behavior: smooth;
    }

    /* Scrollbar styling for sidebar */
    .analytics-sidebar::-webkit-scrollbar {
        width: 8px;
    }

    .analytics-sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .analytics-sidebar::-webkit-scrollbar-thumb {
        background: rgba(102, 126, 234, 0.5);
        border-radius: 4px;
    }

    .analytics-sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(102, 126, 234, 0.7);
    }

    .sidebar-header {
        padding: 0 20px 30px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 30px;
    }

    .sidebar-header h3 {
        color: white;
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu li {
        margin: 0;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #cbd5e0;
        text-decoration: none;
        transition: all 0.3s ease;
        border-right: 3px solid transparent;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .sidebar-menu a:hover {
        background: rgba(102, 126, 234, 0.15);
        color: #667eea;
        border-right-color: #667eea;
        padding-left: 30px;
    }

    .sidebar-menu a.active {
        background: rgba(102, 126, 234, 0.25);
        color: #667eea;
        border-right-color: #667eea;
        padding-left: 30px;
        font-weight: 600;
    }

    .sidebar-menu i {
        width: 20px;
        margin-right: 15px;
        font-size: 16px;
    }

    /* Main Content */
    .analytics-main {
        padding: 30px;
        width: 100%;
        flex: 1;
    }

    .analytics-container {
        padding: 0;
    }

    .page-header {
        background: var(--primary-gradient);
        color: white;
        padding: 40px 0;
        margin-bottom: 30px;
        border-radius: 10px;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
    }

    .page-header p {
        font-size: 1.1rem;
        margin: 10px 0 0 0;
        opacity: 0.9;
    }

    /* KPI Cards */
    .kpi-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-top: 4px solid var(--primary-color);
        position: relative;
        overflow: hidden;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .kpi-card.success { border-top-color: #48bb78; }
    .kpi-card.warning { border-top-color: #ed8936; }
    .kpi-card.danger { border-top-color: #f56565; }

    .kpi-icon {
        font-size: 32px;
        position: absolute;
        right: 20px;
        top: 20px;
        opacity: 0.1;
    }

    .kpi-label {
        color: #718096;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 10px;
    }

    .kpi-change {
        font-size: 0.9rem;
        font-weight: 600;
        color: #48bb78;
    }

    /* Section Card */
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Charts */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 20px;
    }

    .chart-wrapper {
        position: relative;
        height: 300px;
    }

    /* Tables */
    .analytics-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .analytics-table thead { background: #f7fafc; }

    .analytics-table th {
        padding: 15px;
        text-align: left;
        color: #4a5568;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
    }

    .analytics-table td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #2d3748;
    }

    .analytics-table tr:hover { background: #f7fafc; }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-success { background: #c6f6d5; color: #22543d; }
    .badge-warning { background: #feebc8; color: #7c2d12; }
    .badge-danger { background: #fed7d7; color: #742a2a; }
    .badge-info { background: #bee3f8; color: #2c5282; }

    /* Lists */
    .stats-list {
        list-style: none;
        padding: 0;
    }

    .stats-list li {
        padding: 15px 0;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stats-list li:last-child { border-bottom: none; }

    .stats-label { color: #718096; font-weight: 500; }
    .stats-value { font-size: 1.2rem; font-weight: 700; color: #1a202c; }

    /* Responsive */
    @media (max-width: 1024px) {
        .analytics-wrapper { margin-left: 220px; }
        .analytics-sidebar { width: 220px; }
        .charts-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .analytics-wrapper { margin-left: 0; }
        .analytics-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            top: auto;
            overflow-y: visible;
            margin-bottom: 20px;
        }
        .analytics-main {
            width: 100%;
            margin-top: 0;
        }
        .charts-grid { grid-template-columns: 1fr; }
        .page-header h1 { font-size: 1.8rem; }
        .kpi-value { font-size: 1.5rem; }
    }

    .section-hidden { display: none; }
    .section-visible { display: block; }

    /* Ensure footer is not covered by sidebar on this page */
    footer.footer {
        margin-left: 260px;
        clear: both;
        position: relative;
        z-index: 1;
    }

    /* Footer responsive adjustments */
    @media (max-width: 1024px) {
        footer.footer {
            margin-left: 220px;
        }
    }

    @media (max-width: 768px) {
        footer.footer {
            margin-left: 0;
        }
    }
</style>

<div class="analytics-wrapper">
    <!-- Sidebar Navigation -->
    <div class="analytics-sidebar">
        <div class="sidebar-header">
            <h3>📊 Analytics</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="?section=dashboard" class="<?php echo $currentSection === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="?section=revenue" class="<?php echo $currentSection === 'revenue' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Revenue</a></li>
            <li><a href="?section=orders" class="<?php echo $currentSection === 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="?section=products" class="<?php echo $currentSection === 'products' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Products</a></li>
            <li><a href="?section=customers" class="<?php echo $currentSection === 'customers' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Customers</a></li>
            <li><a href="?section=payments" class="<?php echo $currentSection === 'payments' ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i> Payments</a></li>
            <li><a href="?section=reports" class="<?php echo $currentSection === 'reports' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> Reports</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="analytics-main">
        <div class="analytics-container">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="container-fluid">
                    <h1>📊 Analytics Dashboard</h1>
                    <p><?php echo date('l, F j, Y'); ?></p>
                </div>
            </div>

            <div class="container-fluid">
                <!-- ===== DASHBOARD SECTION ===== -->
                <div id="dashboard-section" class="<?php echo $currentSection === 'dashboard' ? 'section-visible' : 'section-hidden'; ?>">
                    <!-- KPI Cards Row 1 -->
                    <div class="row mb-5">
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="kpi-card success">
                                <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                                <div class="kpi-label">Total Revenue</div>
                                <div class="kpi-value">KSh<?php echo number_format($dashboardData['totalRevenue'] ?? 0, 0); ?></div>
                                <div class="kpi-change"><i class="fas fa-arrow-up"></i> +12.5%</div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="kpi-card">
                                <div class="kpi-icon"><i class="fas fa-shopping-cart"></i></div>
                                <div class="kpi-label">Total Orders</div>
                                <div class="kpi-value"><?php echo number_format($dashboardData['totalOrders'] ?? 0); ?></div>
                                <div class="kpi-change"><i class="fas fa-arrow-up"></i> +8.2%</div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="kpi-card">
                                <div class="kpi-icon"><i class="fas fa-users"></i></div>
                                <div class="kpi-label">Total Customers</div>
                                <div class="kpi-value"><?php echo number_format($dashboardData['totalCustomers'] ?? 0); ?></div>
                                <div class="kpi-change"><i class="fas fa-arrow-up"></i> +5.3%</div>
                            </div>
                        </div>
                    </div>

                    <!-- KPI Cards Row 2 -->
                    <div class="row mb-5">
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="kpi-card warning">
                                <div class="kpi-icon"><i class="fas fa-box"></i></div>
                                <div class="kpi-label">Products Sold</div>
                                <div class="kpi-value"><?php echo number_format($dashboardData['totalProductsSold'] ?? 0); ?></div>
                                <div class="kpi-change"><i class="fas fa-arrow-up"></i> +3.8%</div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="kpi-card success">
                                <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                                <div class="kpi-label">Conversion Rate</div>
                                <div class="kpi-value"><?php echo number_format($dashboardData['conversionRate'] ?? 0, 2); ?>%</div>
                                <div class="kpi-change"><i class="fas fa-arrow-up"></i> +2.1%</div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="kpi-card success">
                                <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
                                <div class="kpi-label">Avg Order Value</div>
                                <div class="kpi-value">KSh<?php echo number_format($dashboardData['averageOrderValue'] ?? 0, 0); ?></div>
                                <div class="kpi-change"><i class="fas fa-arrow-up"></i> +6.4%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="charts-grid">
                        <div class="chart-container">
                            <div class="chart-title">📈 Revenue Trend</div>
                            <div class="chart-wrapper">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-container">
                            <div class="chart-title">📊 Order Status Distribution</div>
                            <div class="chart-wrapper">
                                <canvas id="orderStatusChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-container">
                            <div class="chart-title">🏆 Top 5 Products</div>
                            <div class="chart-wrapper">
                                <canvas id="topProductsChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-container">
                            <div class="chart-title">💳 Payment Methods</div>
                            <div class="chart-wrapper">
                                <canvas id="paymentMethodsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== REVENUE SECTION ===== -->
                <div id="revenue-section" class="<?php echo $currentSection === 'revenue' ? 'section-visible' : 'section-hidden'; ?>">
                    <div class="section-card">
                        <h3 class="section-title"><i class="fas fa-chart-line"></i> Revenue Analytics</h3>
                        <ul class="stats-list">
                            <li>
                                <span class="stats-label">Total Revenue</span>
                                <span class="stats-value">KSh<?php echo number_format($revenueData['totalRevenue'] ?? 0, 0); ?></span>
                            </li>
                            <li>
                                <span class="stats-label">Revenue Growth</span>
                                <span class="stats-value"><?php echo number_format($revenueData['revenueGrowth'] ?? 0, 2); ?>%</span>
                            </li>
                            <li>
                                <span class="stats-label">Growth Trend</span>
                                <span class="badge badge-success">
                                    <?php
                                    $trend = $revenueData['growthTrend'] ?? 'STABLE';
                                    echo ($trend === 'UP' ? '📈' : ($trend === 'DOWN' ? '📉' : '→')) . ' ' . htmlspecialchars($trend);
                                    ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- ===== ORDERS SECTION ===== -->
                <div id="orders-section" class="<?php echo $currentSection === 'orders' ? 'section-visible' : 'section-hidden'; ?>">
                    <div class="section-card">
                        <h3 class="section-title"><i class="fas fa-shopping-cart"></i> Order Analytics</h3>
                        <ul class="stats-list">
                            <li><span class="stats-label">Total Orders</span><span class="stats-value"><?php echo number_format($ordersData['totalOrders'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Pending</span><span class="badge badge-warning"><?php echo number_format($ordersData['pendingOrders'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Processing</span><span class="badge badge-info"><?php echo number_format($ordersData['processingOrders'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Confirmed</span><span class="badge badge-success"><?php echo number_format($ordersData['confirmedOrders'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Delivered</span><span class="badge badge-success"><?php echo number_format($ordersData['deliveredOrders'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Cancelled</span><span class="badge badge-danger"><?php echo number_format($ordersData['cancelledOrders'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Avg Processing Time</span><span class="stats-value"><?php echo number_format($ordersData['averageProcessingTime'] ?? 0, 1); ?> hrs</span></li>
                        </ul>
                    </div>
                </div>

                <!-- ===== PRODUCTS SECTION ===== -->
                <div id="products-section" class="<?php echo $currentSection === 'products' ? 'section-visible' : 'section-hidden'; ?>">
                    <div class="section-card">
                        <h3 class="section-title"><i class="fas fa-box"></i> Product Analytics</h3>
                        <ul class="stats-list">
                            <li><span class="stats-label">Total Products</span><span class="stats-value"><?php echo number_format($productsData['totalProducts'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Active</span><span class="badge badge-success"><?php echo number_format($productsData['activeProducts'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Inactive</span><span class="badge badge-warning"><?php echo number_format($productsData['inactiveProducts'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Low Stock</span><span class="badge badge-danger"><?php echo number_format($productsData['lowStockProducts'] ?? 0); ?></span></li>
                        </ul>

                        <h5 class="mt-4 mb-3">🏆 Top 10 Selling Products</h5>
                        <table class="analytics-table">
                            <thead>
                                <tr><th>Product</th><th>Category</th><th>Units Sold</th><th>Revenue</th><th>Stock</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $topSelling = $productsData['topSellingProducts'] ?? [];
                                if (!empty($topSelling)):
                                    foreach (array_slice($topSelling, 0, 10) as $product):
                                        $stock = $product['currentStock'] ?? 0;
                                        $badgeClass = $stock > 20 ? 'badge-success' : 'badge-warning';
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($product['productName'] ?? 'N/A'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                                    <td><?php echo number_format($product['unitsSold'] ?? 0); ?></td>
                                    <td>KSh<?php echo number_format($product['revenue'] ?? 0, 0); ?></td>
                                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $stock; ?></span></td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="5" class="text-center text-muted">No data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <?php if (!empty($productsData['lowStockWarnings'])): ?>
                        <h5 class="mt-4 mb-3">⚠️ Low Stock Alerts</h5>
                        <table class="analytics-table">
                            <thead>
                                <tr><th>Product</th><th>Category</th><th>Stock</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productsData['lowStockWarnings'] as $product): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($product['productName'] ?? 'N/A'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                                    <td><?php echo $product['currentStock'] ?? 0; ?></td>
                                    <td><span class="badge badge-danger">🚨 Critical</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ===== CUSTOMERS SECTION ===== -->
                <div id="customers-section" class="<?php echo $currentSection === 'customers' ? 'section-visible' : 'section-hidden'; ?>">
                    <div class="section-card">
                        <h3 class="section-title"><i class="fas fa-users"></i> Customer Analytics</h3>
                        <ul class="stats-list">
                            <li><span class="stats-label">Total Customers</span><span class="stats-value"><?php echo number_format($customersData['totalCustomers'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Active Customers</span><span class="badge badge-success"><?php echo number_format($customersData['activeCustomers'] ?? 0); ?></span></li>
                            <li><span class="stats-label">New This Month</span><span class="badge badge-info"><?php echo number_format($customersData['newCustomersThisMonth'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Returning Customers</span><span class="badge badge-success"><?php echo number_format($customersData['returningCustomers'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Retention Rate</span><span class="stats-value"><?php echo number_format($customersData['customerRetentionRate'] ?? 0, 2); ?>%</span></li>
                            <li><span class="stats-label">Avg Lifetime Value</span><span class="stats-value">KSh<?php echo number_format($customersData['averageCustomerLifetimeValue'] ?? 0, 0); ?></span></li>
                        </ul>
                    </div>
                </div>

                <!-- ===== PAYMENTS SECTION ===== -->
                <div id="payments-section" class="<?php echo $currentSection === 'payments' ? 'section-visible' : 'section-hidden'; ?>">
                    <div class="section-card">
                        <h3 class="section-title"><i class="fas fa-credit-card"></i> Payment Analytics</h3>
                        <ul class="stats-list">
                            <li><span class="stats-label">Total Payments</span><span class="stats-value"><?php echo number_format($paymentsData['totalPayments'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Successful</span><span class="badge badge-success"><?php echo number_format($paymentsData['successfulPayments'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Failed</span><span class="badge badge-danger"><?php echo number_format($paymentsData['failedPayments'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Pending</span><span class="badge badge-warning"><?php echo number_format($paymentsData['pendingPayments'] ?? 0); ?></span></li>
                            <li><span class="stats-label">Total Value</span><span class="stats-value">KSh<?php echo number_format($paymentsData['totalPaymentValue'] ?? 0, 0); ?></span></li>
                            <li><span class="stats-label">Success Rate</span><span class="stats-value"><?php echo number_format($paymentsData['successRate'] ?? 0, 2); ?>%</span></li>
                        </ul>

                        <?php if (!empty($paymentsData['paymentMethods'])): ?>
                        <h5 class="mt-4 mb-3">Payment Methods</h5>
                        <table class="analytics-table">
                            <thead>
                                <tr><th>Method</th><th>Transactions</th><th>Amount</th><th>%</th><th>Success Rate</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paymentsData['paymentMethods'] as $method => $stats): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars(ucfirst($method)); ?></strong></td>
                                    <td><?php echo number_format($stats['count'] ?? 0); ?></td>
                                    <td>KSh<?php echo number_format($stats['totalAmount'] ?? 0, 0); ?></td>
                                    <td><?php echo number_format($stats['percentage'] ?? 0, 2); ?>%</td>
                                    <td><?php echo number_format($stats['successRate'] ?? 0, 2); ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ===== REPORTS SECTION ===== -->
                <div id="reports-section" class="<?php echo $currentSection === 'reports' ? 'section-visible' : 'section-hidden'; ?>">
                    <div class="section-card">
                        <h3 class="section-title"><i class="fas fa-file-alt"></i> Reports</h3>
                        <div class="alert alert-info" role="alert">
                            <strong>📋 Report Generation Tools</strong>
                            <p>Generate comprehensive reports for your business analytics and metrics.</p>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-line fa-3x mb-3" style="color: #667eea;"></i>
                                        <h5>Revenue Report</h5>
                                        <p class="text-muted small">Download revenue analysis</p>
                                        <a href="ajax/download_report.php?type=revenue" class="btn btn-sm btn-primary" download>
                                            <i class="fas fa-download"></i> Download CSV
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shopping-cart fa-3x mb-3" style="color: #48bb78;"></i>
                                        <h5>Orders Report</h5>
                                        <p class="text-muted small">Download order details</p>
                                        <a href="ajax/download_report.php?type=orders" class="btn btn-sm btn-success" download>
                                            <i class="fas fa-download"></i> Download CSV
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-3x mb-3" style="color: #ed8936;"></i>
                                        <h5>Customers Report</h5>
                                        <p class="text-muted small">Download customer insights</p>
                                        <a href="ajax/download_report.php?type=customers" class="btn btn-sm btn-warning" download>
                                            <i class="fas fa-download"></i> Download CSV
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue (KSh)',
                    data: [50000, 75000, 60000, 85000, 95000, 110000, 105000],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } }
            }
        });
    }

    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart');
    if (orderStatusCtx) {
        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Processing', 'Pending', 'Confirmed', 'Cancelled'],
                datasets: [{
                    data: [
                        <?php echo $ordersData['deliveredOrders'] ?? 0; ?>,
                        <?php echo $ordersData['processingOrders'] ?? 0; ?>,
                        <?php echo $ordersData['pendingOrders'] ?? 0; ?>,
                        <?php echo $ordersData['confirmedOrders'] ?? 0; ?>,
                        <?php echo $ordersData['cancelledOrders'] ?? 0; ?>
                    ],
                    backgroundColor: ['#48bb78', '#667eea', '#ed8936', '#3182ce', '#f56565']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'bottom' } }
            }
        });
    }

    // Top Products Chart
    const topProductsCtx = document.getElementById('topProductsChart');
    if (topProductsCtx) {
        const topSellingProducts = <?php
            $topProducts = array_slice($productsData['topSellingProducts'] ?? [], 0, 5);
            $productLabels = array_map(function($p) { return $p['productName'] ?? 'N/A'; }, $topProducts);
            $productSales = array_map(function($p) { return $p['unitsSold'] ?? 0; }, $topProducts);
            echo json_encode([
                'labels' => $productLabels,
                'data' => $productSales
            ]);
        ?>;

        new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: topSellingProducts.labels.length > 0 ? topSellingProducts.labels : ['No data'],
                datasets: [{
                    label: 'Units Sold',
                    data: topSellingProducts.data.length > 0 ? topSellingProducts.data : [0],
                    backgroundColor: '#667eea'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: true } }
            }
        });
    }

    // Payment Methods Chart
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart');
    if (paymentMethodsCtx) {
        new Chart(paymentMethodsCtx, {
            type: 'bar',
            data: {
                labels: ['Card', 'Bank Transfer', 'Wallet', 'Cash'],
                datasets: [{
                    label: 'Transactions',
                    data: [280, 150, 120, 80],
                    backgroundColor: ['#667eea', '#48bb78', '#ed8936', '#f56565']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } }
            }
        });
    }
</script>

<?php include '../includes/footer.php'; ?>

