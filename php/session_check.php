<?php
/**
 * SESSION CHECK
 * Verifies if user/admin is logged in
 */

require_once 'config.php';

$isAdmin = isset($_GET['admin']) && $_GET['admin'] == '1';

if ($isAdmin) {
    // Check admin session
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        sendJsonResponse([
            'success' => true,
            'loggedIn' => true,
            'isAdmin' => true,
            'admin' => [
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username']
            ]
        ]);
    } else {
        sendJsonResponse([
            'success' => false,
            'loggedIn' => false,
            'isAdmin' => false
        ]);
    }
} else {
    // Check user session
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        sendJsonResponse([
            'success' => true,
            'loggedIn' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'location' => $_SESSION['user_location_name']
            ]
        ]);
    } else {
        sendJsonResponse([
            'success' => false,
            'loggedIn' => false
        ]);
    }
}
?>

