<?php
require_once '../config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['orderId']) || !isset($input['reference'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID and reference are required']);
    exit;
}

$orderId = $input['orderId'];
$reference = $input['reference'];

$response = makeApiRequest(
    '/checkout/confirm-payment/' . $orderId . '?reference=' . urlencode($reference),
    'POST'
);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode([
        'success' => true,
        'data' => $response['data']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to confirm payment'
    ]);
}
