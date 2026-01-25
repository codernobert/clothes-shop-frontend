<?php
require_once '../../config.php';
header('Content-Type: application/json');

$productId = $_GET['id'] ?? null;
$input = json_decode(file_get_contents('php://input'), true);

if (!$productId || !isset($input['name'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$response = makeApiRequest('/admin/products/' . $productId, 'PUT', $input);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true, 'data' => $response['data']]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to update product'
    ]);
}