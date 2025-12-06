<?php
/**
 * GET ADMIN STATISTICS
 * Returns admin panel statistics
 */

require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    sendJsonResponse(['success' => false, 'message' => 'Not authorized']);
}

$stats = [];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
$stats['totalUsers'] = $row['count'];

// Total locations with users
$result = $conn->query("
    SELECT COUNT(DISTINCT location_id) as count 
    FROM users
");
$row = $result->fetch_assoc();
$stats['totalLocations'] = $row['count'];

// Events today
$result = $conn->query("
    SELECT COUNT(*) as count 
    FROM water_events 
    WHERE DATE(created_at) = CURDATE()
");
$row = $result->fetch_assoc();
$stats['eventsToday'] = $row['count'];

// Emails sent today (approximate: events * average users per location)
$result = $conn->query("
    SELECT SUM(
        (SELECT COUNT(*) FROM users WHERE location_id = we.location_id)
    ) as total
    FROM water_events we
    WHERE DATE(we.created_at) = CURDATE()
");
$row = $result->fetch_assoc();
$stats['emailsSentToday'] = $row['total'] ?? 0;

// Recent events
$result = $conn->query("
    SELECT we.*, l.location_name, a.username as admin_username,
    (SELECT COUNT(*) FROM users WHERE location_id = we.location_id) as emails_sent
    FROM water_events we
    JOIN locations l ON we.location_id = l.id
    LEFT JOIN admins a ON we.admin_id = a.id
    ORDER BY we.created_at DESC
    LIMIT 10
");

$recentEvents = [];
while ($row = $result->fetch_assoc()) {
    $recentEvents[] = $row;
}

// Users by location
$result = $conn->query("
    SELECT l.id, l.location_name, 
    COUNT(u.id) as user_count,
    (SELECT MAX(CONCAT(we.arrival_date, ' ', we.arrival_time))
     FROM water_events we 
     WHERE we.location_id = l.id) as last_event
    FROM locations l
    LEFT JOIN users u ON l.id = u.location_id
    GROUP BY l.id, l.location_name
    HAVING user_count > 0
    ORDER BY user_count DESC
");

$usersByLocation = [];
while ($row = $result->fetch_assoc()) {
    if ($row['last_event']) {
        $date = new DateTime($row['last_event']);
        $row['last_event'] = $date->format('M j, Y g:i A');
    }
    $usersByLocation[] = $row;
}

sendJsonResponse([
    'success' => true,
    'stats' => $stats,
    'recentEvents' => $recentEvents,
    'usersByLocation' => $usersByLocation
]);
?>

