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

if (!$userId || !$itemId) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// Make API request with authentication
$response = makeApiRequest('/cart/' . $userId . '/items/' . $itemId, 'DELETE', null, true);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
}

