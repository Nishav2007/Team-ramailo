<?php
/**
 * DATABASE CONFIGURATION EXAMPLE
 * Copy this file to config.php and update with your settings
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Default XAMPP MySQL username
define('DB_PASS', '');                // Default XAMPP MySQL password (empty)
define('DB_NAME', 'melamchi_alert');  // Database name

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Set charset to UTF-8
$conn->set_charset('utf8mb4');

// Email configuration (Gmail SMTP)
// For testing: Leave as is (PHP mail() will be used)
// For production: Update with your Gmail credentials
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');  // Replace with your Gmail
define('SMTP_PASSWORD', 'your-app-password');      // Replace with Gmail App Password
define('SMTP_FROM_EMAIL', 'alerts@melamchialert.com');
define('SMTP_FROM_NAME', 'Melamchi Water Alert System');

// Application settings
define('SITE_URL', 'http://localhost/GoodDream');
define('SITE_NAME', 'Melamchi Water Alert System');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to send JSON response
function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Helper function to sanitize input
function sanitizeInput($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}
?>

