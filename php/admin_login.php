<?php
/**
 * ADMIN LOGIN
 * Handles admin authentication
 */

require_once 'config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'message' => 'Invalid request method']);
}

// Get POST data
$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
    sendJsonResponse(['success' => false, 'message' => 'Username and password are required']);
}

// Get admin from database
$stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    sendJsonResponse(['success' => false, 'message' => 'Invalid username or password']);
}

$admin = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $admin['password'])) {
    sendJsonResponse(['success' => false, 'message' => 'Invalid username or password']);
}

// Create admin session
$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_username'] = $admin['username'];
$_SESSION['is_admin'] = true;

sendJsonResponse([
    'success' => true,
    'message' => 'Admin login successful',
    'admin' => [
        'id' => $admin['id'],
        'username' => $admin['username']
    ]
]);
?>

