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

$productId = $_GET['id'] ?? null;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

$response = makeApiRequest('/admin/products/' . $productId, 'DELETE', null, true);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to delete product'
    ]);
}


