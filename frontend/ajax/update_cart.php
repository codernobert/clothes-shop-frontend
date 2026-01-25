<?php
require_once '../config.php';
header('Content-Type: application/json');

$userId = $_GET['userId'] ?? null;
$itemId = $_GET['itemId'] ?? null;
$quantity = $_GET['quantity'] ?? null;

if (!$userId || !$itemId || !$quantity) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$response = makeApiRequest('/cart/' . $userId . '/items/' . $itemId . '?quantity=' . $quantity, 'PUT');

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
}
