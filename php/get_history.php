<?php
/**
 * GET HISTORY
 * Returns water supply history for user's location
 */

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    sendJsonResponse(['success' => false, 'message' => 'Not logged in']);
}

$location_id = $_SESSION['user_location_id'];
$days = isset($_GET['days']) && $_GET['days'] !== 'all' ? intval($_GET['days']) : null;

// Build query
if ($days) {
    $stmt = $conn->prepare("
        SELECT we.*, l.location_name 
        FROM water_events we 
        JOIN locations l ON we.location_id = l.id 
        WHERE we.location_id = ? 
        AND we.arrival_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ORDER BY we.arrival_date DESC, we.arrival_time DESC
    ");
    $stmt->bind_param('ii', $location_id, $days);
} else {
    $stmt = $conn->prepare("
        SELECT we.*, l.location_name 
        FROM water_events we 
        JOIN locations l ON we.location_id = l.id 
        WHERE we.location_id = ? 
        ORDER BY we.arrival_date DESC, we.arrival_time DESC
    ");
    $stmt->bind_param('i', $location_id);
}

$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

// Calculate statistics
$stats = [];
$stats['totalEvents'] = count($events);

// Calculate average frequency
if (count($events) > 1) {
    $first = strtotime($events[count($events) - 1]['arrival_date']);
    $last = strtotime($events[0]['arrival_date']);
    $daysDiff = ($last - $first) / (60 * 60 * 24);
    $avgGap = $daysDiff / (count($events) - 1);
    $stats['avgFrequency'] = round($avgGap, 1) . ' days';
} else {
    $stats['avgFrequency'] = 'N/A';
}

// Most common day
$dayCount = [];
foreach ($events as $event) {
    $dayName = date('l', strtotime($event['arrival_date']));
    $dayCount[$dayName] = ($dayCount[$dayName] ?? 0) + 1;
}
if (!empty($dayCount)) {
    arsort($dayCount);
    $stats['mostCommonDay'] = key($dayCount);
} else {
    $stats['mostCommonDay'] = 'N/A';
}

// Most common hour
$hourCount = [];
foreach ($events as $event) {
    $hour = date('H', strtotime($event['arrival_time']));
    $hourCount[$hour] = ($hourCount[$hour] ?? 0) + 1;
}
if (!empty($hourCount)) {
    arsort($hourCount);
    $mostCommonHour = key($hourCount);
    $ampm = $mostCommonHour >= 12 ? 'PM' : 'AM';
    $hour12 = $mostCommonHour % 12 ?: 12;
    $stats['mostCommonHour'] = $hour12 . ':00 ' . $ampm;
} else {
    $stats['mostCommonHour'] = 'N/A';
}

sendJsonResponse([
    'success' => true,
    'events' => $events,
    'stats' => $stats,
    'count' => count($events)
]);
?>

