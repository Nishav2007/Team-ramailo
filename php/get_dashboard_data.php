<?php
/**
 * GET DASHBOARD DATA
 * Returns user dashboard information
 */

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    sendJsonResponse(['success' => false, 'message' => 'Not logged in']);
}

$user_id = $_SESSION['user_id'];
$location_id = $_SESSION['user_location_id'];

// Get latest water event for user's location
$stmt = $conn->prepare("
    SELECT we.*, l.location_name 
    FROM water_events we 
    JOIN locations l ON we.location_id = l.id 
    WHERE we.location_id = ? 
    ORDER BY we.arrival_date DESC, we.arrival_time DESC 
    LIMIT 1
");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$result = $stmt->get_result();
$latestEvent = $result->fetch_assoc();

// Get recent events (last 5)
$stmt = $conn->prepare("
    SELECT we.*, l.location_name 
    FROM water_events we 
    JOIN locations l ON we.location_id = l.id 
    WHERE we.location_id = ? 
    ORDER BY we.arrival_date DESC, we.arrival_time DESC 
    LIMIT 5
");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$result = $stmt->get_result();
$recentEvents = [];
while ($row = $result->fetch_assoc()) {
    $recentEvents[] = $row;
}

// Calculate statistics
$stats = [];

// Last supply
if ($latestEvent) {
    $date = new DateTime($latestEvent['arrival_date']);
    $stats['lastSupply'] = $date->format('M j, Y');
} else {
    $stats['lastSupply'] = 'No data';
}

// Average frequency (last 30 days)
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM water_events 
    WHERE location_id = ? 
    AND arrival_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$eventCount = $row['count'];

if ($eventCount > 1) {
    $avgDays = 30 / $eventCount;
    $stats['frequency'] = 'Every ' . round($avgDays, 1) . ' days';
} else {
    $stats['frequency'] = 'Insufficient data';
}

// Most common arrival time
$stmt = $conn->prepare("
    SELECT HOUR(arrival_time) as hour, COUNT(*) as count 
    FROM water_events 
    WHERE location_id = ? 
    GROUP BY HOUR(arrival_time) 
    ORDER BY count DESC 
    LIMIT 1
");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $hour = $row['hour'];
    $ampm = $hour >= 12 ? 'PM' : 'AM';
    $hour12 = $hour % 12 ?: 12;
    $stats['commonTime'] = $hour12 . ':00 ' . $ampm;
} else {
    $stats['commonTime'] = 'No data';
}

// Total events (last 30 days)
$stats['totalEvents'] = $eventCount;

sendJsonResponse([
    'success' => true,
    'user' => [
        'name' => $_SESSION['user_name'],
        'location' => $_SESSION['user_location_name']
    ],
    'latestEvent' => $latestEvent,
    'recentEvents' => $recentEvents,
    'stats' => $stats
]);
?>

