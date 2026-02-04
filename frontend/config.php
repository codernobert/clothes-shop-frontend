<?php
// API Configuration
// Note: session_start() should be called by each page BEFORE including header.php

// Use environment variable if available, otherwise default to localhost
$apiBaseUrl = getenv('API_BASE_URL') ?: 'http://localhost:8080/api';
define('API_BASE_URL', $apiBaseUrl);

// Helper function to make API requests with JWT authentication
function makeApiRequest($endpoint, $method = 'GET', $data = null, $requireAuth = false) {
    $url = API_BASE_URL . $endpoint;

    $headers = "Content-Type: application/json\r\n";

    // Add JWT token if required or available
    if ($requireAuth || isset($_SESSION['access_token'])) {
        $token = $_SESSION['access_token'] ?? null;
        if ($token) {
            $headers .= "Authorization: Bearer $token\r\n";
        } elseif ($requireAuth) {
            return ['error' => 'Authentication required', 'redirect' => 'login.php'];
        }
    }

    $options = [
        'http' => [
            'method' => $method,
            'header' => $headers,
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
    if (PHP_VERSION_ID >= 80400 && function_exists('http_get_last_response_headers')) {
        $responseHeaders = http_get_last_response_headers();
    } else {
        $responseHeaders = $http_response_header ?? null;
    }

    if (isset($responseHeaders) && !empty($responseHeaders)) {
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $responseHeaders[0], $matches);
        $httpCode = isset($matches[1]) ? (int)$matches[1] : 0;

        // Handle 401 Unauthorized
        if ($httpCode === 401 && $requireAuth) {
            // Clear session and redirect to login
            session_destroy();
            return ['error' => 'Session expired', 'redirect' => 'login.php'];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
    }

    return null;
}

// Check if user is authenticated
function isAuthenticated() {
    return isset($_SESSION['access_token']) && isset($_SESSION['user']);
}

// Get current user from session
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

// Get user ID from session
function getUserId() {
    $user = getCurrentUser();
    return $user['userId'] ?? null;
}

// Check if user has admin role
function isAdmin() {
    $user = getCurrentUser();
    return isset($user['role']) && $user['role'] === 'ADMIN';
}

// Require authentication (redirect if not logged in)
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit;
    }
}

// Require admin authentication (redirect if not logged in or not admin)
function requireAdminAuth() {
    if (!isAuthenticated()) {
        header('Location: ../admin/login.php');
        exit;
    }

    if (!isAdmin()) {
        header('Location: ../admin/login.php');
        exit;
    }
}

// Login user and store tokens
function loginUser($authResponse) {
    $_SESSION['access_token'] = $authResponse['accessToken'];
    $_SESSION['refresh_token'] = $authResponse['refreshToken'];
    $_SESSION['user'] = [
        'userId' => $authResponse['userId'],
        'email' => $authResponse['email'],
        'firstName' => $authResponse['firstName'],
        'lastName' => $authResponse['lastName'],
        'role' => $authResponse['role']
    ];
}

// Logout user
function logoutUser() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Format currency
function formatCurrency($amount) {
    return 'Ksh' . number_format($amount, 2);
}
?>