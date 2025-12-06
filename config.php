<?php
/**
 * MELAMCHI WATER ALERT SYSTEM - CONFIGURATION
 * Database connection and core functions
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'melamchi_alert');

// Site configuration
define('SITE_URL', 'http://localhost/GoodDream');
define('SITE_NAME', 'Melamchi Water Alert');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("<h1>Database Connection Failed</h1><p>Error: " . $conn->connect_error . "</p>");
}

$conn->set_charset('utf8mb4');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sanitize input
function clean($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Check if admin is logged in (hardcoded check)
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect if not logged in
function requireLogin() {
    if (!isUserLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin-login.php');
        exit;
    }
}
?>

