<?php
/**
 * USER REGISTRATION
 * Handles new user registration
 */

require_once 'config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'message' => 'Invalid request method']);
}

// Get POST data
$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$location_id = intval($_POST['location_id'] ?? 0);

// Validate input
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters';
}

if ($location_id <= 0) {
    $errors[] = 'Please select a valid location';
}

if (!empty($errors)) {
    sendJsonResponse(['success' => false, 'message' => implode(', ', $errors)]);
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    sendJsonResponse(['success' => false, 'message' => 'Email already registered']);
}

// Verify location exists
$stmt = $conn->prepare("SELECT id FROM locations WHERE id = ?");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    sendJsonResponse(['success' => false, 'message' => 'Invalid location selected']);
}

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (name, email, password, location_id, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param('sssi', $name, $email, $password_hash, $location_id);

if ($stmt->execute()) {
    sendJsonResponse([
        'success' => true,
        'message' => 'Registration successful!',
        'user_id' => $conn->insert_id
    ]);
} else {
    sendJsonResponse(['success' => false, 'message' => 'Registration failed. Please try again.']);
}
?>

