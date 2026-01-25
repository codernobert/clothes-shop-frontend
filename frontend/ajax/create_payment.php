<?php
require_once '../config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['amount']) || !isset($input['currency'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$response = makeApiRequest('/checkout/payment-intent', 'POST', $input);

if ($response && isset($response['success']) && $response['success']) {
    echo json_encode([
        'success' => true,
        'authorizationUrl' => $response['data']['authorizationUrl'] ?? null,
        'reference' => $response['data']['reference'] ?? null
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $response['message'] ?? 'Failed to initialize payment'
    ]);
}
?>