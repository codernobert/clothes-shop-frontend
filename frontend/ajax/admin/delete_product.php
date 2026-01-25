<?php
require_once '../../config.php';
header('Content-Type: application/json');

$productId = $_GET['id'] ?? null;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

$response = makeApiRequest('/admin/products/' . $productId, 'DELETE');

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to delete product'
    ]);
}
