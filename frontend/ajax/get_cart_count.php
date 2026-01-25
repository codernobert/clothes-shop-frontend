<?php
require_once '../config.php';
header('Content-Type: application/json');

$userId = getUserId();
$response = makeApiRequest('/cart/' . $userId);

$count = 0;
if ($response && isset($response['data']['items'])) {
    $count = count($response['data']['items']);
}

echo json_encode(['count' => $count]);



