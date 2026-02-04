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

$userId = $_GET['userId'] ?? null;
$itemId = $_GET['itemId'] ?? null;
$quantity = $_GET['quantity'] ?? null;

if (!$userId || !$itemId || !$quantity) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// Make API request with authentication
$response = makeApiRequest('/cart/' . $userId . '/items/' . $itemId . '?quantity=' . $quantity, 'PUT', null, true);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
}
