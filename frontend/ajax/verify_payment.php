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

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    echo json_encode(['success' => false, 'message' => 'Reference is required']);
    exit;
}

// Make API request with authentication
$response = makeApiRequest('/checkout/verify-payment?reference=' . urlencode($reference), 'POST', null, true);

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
