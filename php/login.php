<?php
/**
 * USER LOGIN
 * Handles user authentication
 */

require_once 'config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'message' => 'Invalid request method']);
}

// Get POST data
$email = sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    sendJsonResponse(['success' => false, 'message' => 'Email and password are required']);
}

// Get user from database
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.password, u.location_id, l.location_name 
    FROM users u 
    JOIN locations l ON u.location_id = l.id 
    WHERE u.email = ?
");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    sendJsonResponse(['success' => false, 'message' => 'Invalid email or password']);
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    sendJsonResponse(['success' => false, 'message' => 'Invalid email or password']);
}

// Create session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_location_id'] = $user['location_id'];
$_SESSION['user_location_name'] = $user['location_name'];
$_SESSION['logged_in'] = true;

sendJsonResponse([
    'success' => true,
    'message' => 'Login successful',
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'location' => $user['location_name']
    ]
]);
?>

