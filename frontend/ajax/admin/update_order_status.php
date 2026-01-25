<?php
require_once '../../config.php';
header('Content-Type: application/json');

$orderId = $_GET['orderId'] ?? null;
$status = $_GET['status'] ?? null;

if (!$orderId || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$response = makeApiRequest('/admin/orders/' . $orderId . '/status?status=' . $status, 'PATCH');

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true, 'data' => $response['data']]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to update order status'
    ]);
}
?>