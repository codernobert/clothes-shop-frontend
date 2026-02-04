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

if (!isset($input['userId']) || !isset($input['productId']) || !isset($input['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$data = [
    'productId' => $input['productId'],
    'quantity' => $input['quantity']
];

$response = makeApiRequest('/cart/' . $input['userId'] . '/items', 'POST', $data, true);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to add product to cart'
    ]);
}
?>