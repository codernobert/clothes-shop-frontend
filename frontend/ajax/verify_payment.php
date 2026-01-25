<?php
require_once '../config.php';
header('Content-Type: application/json');

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    echo json_encode(['success' => false, 'message' => 'Reference is required']);
    exit;
}

$response = makeApiRequest('/checkout/verify-payment?reference=' . urlencode($reference), 'POST');

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode([
        'success' => true,
        'data' => $response['data']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Payment verification failed'
    ]);
}
