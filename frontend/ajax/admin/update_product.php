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
$input = json_decode(file_get_contents('php://input'), true);

if (!$productId || !isset($input['name'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$response = makeApiRequest('/admin/products/' . $productId, 'PUT', $input, true);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true, 'data' => $response['data']]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to update product'
    ]);
}

