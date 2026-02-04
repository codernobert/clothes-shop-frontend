<?php
session_start();
require_once '../../config.php';
header('Content-Type: application/json');

// Check authentication
if (!isAuthenticated()) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required',
        'redirect' => '../../login.php'
    ]);
    exit;
}

// Check admin role
if (!isAdmin()) {
    echo json_encode([
        'success' => false,
        'message' => 'Admin access required'
    ]);
    exit;
}

$orderId = $_GET['orderId'] ?? null;
$status = $_GET['status'] ?? null;

if (!$orderId || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$response = makeApiRequest('/admin/orders/' . $orderId . '/status?status=' . $status, 'PATCH', null, true);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true, 'data' => $response['data']]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to update order status'
    ]);
}
?>