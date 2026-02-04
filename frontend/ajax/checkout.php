<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

// Check authentication
if (!isAuthenticated()) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required',
        'redirect' => '../login.php'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['userId']) || !isset($input['shippingAddress']) || !isset($input['paymentMethod'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Make API request with authentication
$response = makeApiRequest('/checkout', 'POST', $input, true);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode([
        'success' => true,
        'orderId' => $response['data']['id'],
        'orderNumber' => $response['data']['orderNumber']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to create order'
    ]);
}
