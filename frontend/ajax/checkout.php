<?php
require_once '../config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['userId']) || !isset($input['shippingAddress']) || !isset($input['paymentMethod'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$response = makeApiRequest('/checkout', 'POST', $input);

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
