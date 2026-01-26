<?php
// API Configuration
// Note: session_start() should be called by each page BEFORE including header.php
define('API_BASE_URL', 'http://localhost:8080/api');

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
    // Handle $http_response_header for PHP 8.4+ compatibility
    if (PHP_VERSION_ID >= 80400 && function_exists('http_get_last_response_headers')) {
        $responseHeaders = http_get_last_response_headers();
    } else {
        $responseHeaders = $http_response_header ?? null;
    }

    if (isset($responseHeaders) && !empty($responseHeaders)) {
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $responseHeaders[0], $matches);
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