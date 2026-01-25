<?php
// API Configuration
define('API_BASE_URL', 'http://localhost:8080/api');

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to make API requests
function makeApiRequest($endpoint, $method = 'GET', $data = null) {
    $url = API_BASE_URL . $endpoint;

    $options = [
        'http' => [
            'method' => $method,
            'header' => "Content-Type: application/json\r\n",
            'ignore_errors' => true,
            'timeout' => 30
        ]
    ];

    if ($data !== null) {
        $jsonData = json_encode($data);
        $options['http']['content'] = $jsonData;
        $options['http']['header'] .= "Content-Length: " . strlen($jsonData) . "\r\n";
    }

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return null;
    }

    // Parse HTTP response code from headers
    if (isset($http_response_header)) {
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $http_response_header[0], $matches);
        $httpCode = isset($matches[1]) ? (int)$matches[1] : 0;

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
    }

    return null;
}

// Get or create user session
function getUserId() {
    if (!isset($_SESSION['user_id'])) {
        // For demo purposes, create a default user ID
        // In production, this should be from authentication
        $_SESSION['user_id'] = 1;
    }
    return $_SESSION['user_id'];
}

// Format currency
function formatCurrency($amount) {
    return 'KSh ' . number_format($amount, 2);
}
?>