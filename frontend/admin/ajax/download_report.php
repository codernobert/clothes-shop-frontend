<?php
// Suppress output buffering and errors before session start
ob_start();
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 0);

session_start();
require_once '../../config.php';

// Require admin authentication
requireAdminAuth();

// Get JWT token from session
$token = $_SESSION['access_token'] ?? null;

if (!$token) {
    ob_end_clean();
    header('HTTP/1.1 401 Unauthorized');
    echo 'Unauthorized';
    exit;
}

// Get report type from query parameter
$reportType = $_GET['type'] ?? null;

if (!$reportType) {
    ob_end_clean();
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid report type';
    exit;
}

// Function to fetch analytics data
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

// Function to output CSV
function outputCSV($filename, $headers, $rows) {
    // Clear any output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Write BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Write headers with proper escape parameter
    fputcsv($output, $headers, ',', '"');

    // Write data rows with proper escape parameter
    foreach ($rows as $row) {
        // Ensure all values are strings and handle empty values properly
        $cleanRow = array_map(function($value) {
            return $value === '' ? '' : (string)$value;
        }, $row);
        fputcsv($output, $cleanRow, ',', '"');
    }

    // Close stream
    fclose($output);
    exit;
}

// Generate Revenue Report
if ($reportType === 'revenue') {
    $revenueData = getAnalyticsData('revenue?period=daily');
    $data = $revenueData['data'] ?? [];

    $headers = ['Date', 'Total Revenue (KSh)', 'Revenue Growth (%)', 'Growth Trend'];
    $rows = [];

    // Extract actual totals from API
    $totalRevenue = $data['totalRevenue'] ?? 0;
    $revenueGrowth = $data['revenueGrowth'] ?? 0;
    $growthTrend = $data['growthTrend'] ?? 'STABLE';

    // Add summary rows
    $rows[] = ['REVENUE SUMMARY', '', '', ''];
    $rows[] = ['Total Revenue', number_format($totalRevenue, 2), '', ''];
    $rows[] = ['Revenue Growth', number_format($revenueGrowth, 2), '%', $growthTrend];
    $rows[] = ['', '', '', ''];
    $rows[] = ['Daily Revenue Data', '', '', ''];

    // Add daily data from API
    if (!empty($data['dailyRevenue']) && is_array($data['dailyRevenue'])) {
        $previousAmount = 0;
        foreach ($data['dailyRevenue'] as $day) {
            $amount = (float)($day['amount'] ?? 0);
            $growth = $previousAmount > 0 ? (($amount - $previousAmount) / $previousAmount * 100) : 0;

            $rows[] = [
                $day['label'] ?? $day['date'] ?? 'N/A',
                number_format($amount, 2),
                number_format($growth, 2),
                'STABLE'
            ];

            $previousAmount = $amount;
        }
    } else {
        // Fallback only if no API data available
        $rows[] = ['No revenue data available', '', '', ''];
    }

    // Add timestamp
    $rows[] = ['', '', '', ''];
    $rows[] = ['Generated', date('Y-m-d H:i:s'), '', ''];

    $filename = 'revenue_report_' . date('Y-m-d_H-i-s') . '.csv';
    outputCSV($filename, $headers, $rows);
}

// Generate Orders Report
elseif ($reportType === 'orders') {
    $ordersData = getAnalyticsData('orders');
    $data = $ordersData['data'] ?? [];

    $headers = ['Order Metric', 'Count', 'Percentage', 'Notes'];
    $rows = [];

    $total = $data['totalOrders'] ?? 0;

    $rows[] = ['ORDER SUMMARY', '', '', ''];
    $rows[] = ['Total Orders', $total, '100%', ''];
    $rows[] = ['', '', '', ''];
    $rows[] = ['ORDER STATUS BREAKDOWN', '', '', ''];

    $statuses = [
        'Pending Orders' => $data['pendingOrders'] ?? 0,
        'Processing Orders' => $data['processingOrders'] ?? 0,
        'Confirmed Orders' => $data['confirmedOrders'] ?? 0,
        'Delivered Orders' => $data['deliveredOrders'] ?? 0,
        'Cancelled Orders' => $data['cancelledOrders'] ?? 0,
    ];

    foreach ($statuses as $status => $count) {
        $percentage = $total > 0 ? ($count / $total * 100) : 0;
        $rows[] = [$status, $count, number_format($percentage, 2) . '%', ''];
    }

    $rows[] = ['', '', '', ''];
    $rows[] = ['ADDITIONAL METRICS', '', '', ''];
    $rows[] = ['Average Processing Time', number_format($data['averageProcessingTime'] ?? 0, 1), 'hours', ''];

    $rows[] = ['', '', '', ''];
    $rows[] = ['Generated', date('Y-m-d H:i:s'), '', ''];

    $filename = 'orders_report_' . date('Y-m-d_H-i-s') . '.csv';
    outputCSV($filename, $headers, $rows);
}

// Generate Customers Report
elseif ($reportType === 'customers') {
    $customersData = getAnalyticsData('customers');
    $data = $customersData['data'] ?? [];

    $headers = ['Metric', 'Value', 'Unit', 'Notes'];
    $rows = [];

    $rows[] = ['CUSTOMER ANALYTICS SUMMARY', '', '', ''];
    $rows[] = ['Total Customers', $data['totalCustomers'] ?? 0, 'customers', ''];
    $rows[] = ['Active Customers', $data['activeCustomers'] ?? 0, 'customers', 'Customers with purchases in last 30 days'];
    $rows[] = ['New Customers This Month', $data['newCustomersThisMonth'] ?? 0, 'customers', ''];
    $rows[] = ['Returning Customers', $data['returningCustomers'] ?? 0, 'customers', 'Made more than one purchase'];
    $rows[] = ['', '', '', ''];
    $rows[] = ['PERFORMANCE METRICS', '', '', ''];
    $rows[] = ['Customer Retention Rate', number_format($data['customerRetentionRate'] ?? 0, 2), '%', 'Percentage of returning customers'];
    $rows[] = ['Average Lifetime Value', 'KSh ' . number_format($data['averageCustomerLifetimeValue'] ?? 0, 0), 'KES', 'Average revenue per customer'];

    $rows[] = ['', '', '', ''];
    $rows[] = ['Generated', date('Y-m-d H:i:s'), '', ''];

    $filename = 'customers_report_' . date('Y-m-d_H-i-s') . '.csv';
    outputCSV($filename, $headers, $rows);
}

else {
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid report type: ' . htmlspecialchars($reportType);
    exit;
}
