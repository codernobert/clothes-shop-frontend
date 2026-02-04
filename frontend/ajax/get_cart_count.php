<?php
session_start();
require_once '../config.php';
header('Content-Type: application/json');

// Check if user is authenticated
if (!isAuthenticated()) {
    echo json_encode(['count' => 0]);
    exit;
}

$userId = getUserId();

if (!$userId) {
    echo json_encode(['count' => 0]);
    exit;
}

// Make API request with authentication (pass true for requireAuth)
$response = makeApiRequest('/cart/' . $userId, 'GET', null, true);

$count = 0;
if ($response && isset($response['data']['items'])) {
    $count = count($response['data']['items']);
} elseif ($response && isset($response['items'])) {
    $count = count($response['items']);
}

echo json_encode(['count' => $count]);
?>
