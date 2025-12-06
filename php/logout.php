<?php
/**
 * LOGOUT
 * Destroys user/admin session
 */

session_start();

// Destroy all session data
session_unset();
session_destroy();

// Send success response
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>

