<?php
session_start();
require_once 'config.php';

// Call logout API if needed (optional since JWT is stateless)
if (isset($_SESSION['access_token'])) {
    makeApiRequest('/auth/logout', 'POST', null, true);
}

// Destroy session
logoutUser();
?>
