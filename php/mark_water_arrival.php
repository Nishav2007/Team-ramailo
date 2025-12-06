<?php
/**
 * MARK WATER ARRIVAL
 * Records water arrival and sends email notifications
 */

require_once 'config.php';
require_once 'send_email_alerts.php';

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    sendJsonResponse(['success' => false, 'message' => 'Not authorized']);
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'message' => 'Invalid request method']);
}

$location_id = intval($_POST['location_id'] ?? 0);
$admin_id = $_SESSION['admin_id'];

// Validate location
if ($location_id <= 0) {
    sendJsonResponse(['success' => false, 'message' => 'Invalid location']);
}

// Verify location exists
$stmt = $conn->prepare("SELECT location_name FROM locations WHERE id = ?");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    sendJsonResponse(['success' => false, 'message' => 'Location not found']);
}

$location = $result->fetch_assoc();
$location_name = $location['location_name'];

// Insert water event
$stmt = $conn->prepare("
    INSERT INTO water_events (location_id, arrival_date, arrival_time, admin_id, created_at) 
    VALUES (?, CURDATE(), CURTIME(), ?, NOW())
");
$stmt->bind_param('ii', $location_id, $admin_id);

if (!$stmt->execute()) {
    sendJsonResponse(['success' => false, 'message' => 'Failed to record water arrival']);
}

$event_id = $conn->insert_id;

// Get all users in this location
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE location_id = ?");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Send email alerts
$emailsSent = 0;
$emailErrors = [];

foreach ($users as $user) {
    try {
        $emailResult = sendWaterAlert($user['email'], $user['name'], $location_name);
        if ($emailResult['success']) {
            $emailsSent++;
        } else {
            $emailErrors[] = $user['email'];
        }
    } catch (Exception $e) {
        $emailErrors[] = $user['email'] . ' (' . $e->getMessage() . ')';
    }
}

sendJsonResponse([
    'success' => true,
    'message' => "Water arrival recorded successfully! {$emailsSent} email alerts sent.",
    'event_id' => $event_id,
    'location' => $location_name,
    'emailsSent' => $emailsSent,
    'totalUsers' => count($users),
    'emailErrors' => $emailErrors
]);
?>

