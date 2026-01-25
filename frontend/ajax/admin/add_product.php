<?php
require_once '../../config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['name']) || !isset($input['price']) || !isset($input['category'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$response = makeApiRequest('/admin/products', 'POST', $input);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode(['success' => true, 'data' => $response['data']]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to create product'
    ]);
}


