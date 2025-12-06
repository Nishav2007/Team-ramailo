<?php
/**
 * USER DASHBOARD - Live Water Flow Status
 * Auto-refreshes every 30 seconds
 */
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$location_id = $_SESSION['user_location_id'];
$location_name = $_SESSION['user_location_name'];

// Get current water status (LIVE)
$stmt = $conn->prepare("SELECT water_status, status_updated_at FROM locations WHERE id = ?");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$locationData = $stmt->get_result()->fetch_assoc();
$waterStatus = $locationData['water_status'] ?? 'not_flowing';
$statusUpdated = $locationData['status_updated_at'] ?? null;

// Get latest water event
$stmt = $conn->prepare("
    SELECT * FROM water_events 
    WHERE location_id = ? 
    ORDER BY arrival_date DESC, arrival_time DESC 
    LIMIT 1
");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$latestEvent = $stmt->get_result()->fetch_assoc();

// Get recent events (last 5)
$stmt = $conn->prepare("
    SELECT * FROM water_events 
    WHERE location_id = ? 
    ORDER BY arrival_date DESC, arrival_time DESC 
    LIMIT 5
");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$recentEvents = [];
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recentEvents[] = $row;
}

// Calculate statistics
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM water_events 
    WHERE location_id = ? 
    AND arrival_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$eventCount = $stmt->get_result()->fetch_assoc()['count'];

// Determine status display priority
$statusClass = 'no-water';
$statusIcon = '❌';
$statusTitle = 'No Recent Water Supply';
$statusMessage = 'No water events recorded recently';
$statusTime = 'You will receive notifications when water arrives';

if ($waterStatus === 'flowing') {
    // PRIORITY 1: Water is flowing RIGHT NOW
    $statusClass = 'flowing';
    $statusIcon = '💧';
    $statusTitle = 'Water Flowing Now!';
    $statusMessage = "Melamchi water is currently flowing in {$location_name}";
    if ($statusUpdated) {
        $statusTime = 'Started: ' . date('M j, Y \a\t g:i A', strtotime($statusUpdated));
    } else {
        $statusTime = 'Collect water immediately!';
    }
} elseif ($latestEvent) {
    // PRIORITY 2: Recent water event exists
    $statusClass = 'available';
    $statusIcon = '💧';
    $statusTitle = 'Water Available!';
    $statusMessage = "Water arrived in {$location_name}";
    $statusTime = 'Last arrival: ' . date('M j, Y \a\t g:i A', strtotime($latestEvent['arrival_date'] . ' ' . $latestEvent['arrival_time']));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="30"><!-- Auto-refresh every 30 seconds -->
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">💧 <?= SITE_NAME ?></a>
            <div class="navbar-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="history.php">History</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="text-center" style="margin: 2rem 0;">
            <h1>Welcome, <?= htmlspecialchars($user_name) ?>!</h1>
            <p style="font-size: 1.1rem; color: #666;">
                Water supply dashboard for <strong><?= htmlspecialchars($location_name) ?></strong>
            </p>
            <div style="margin-top: 1rem;">
                <span class="live-badge">
                    <span class="pulse-dot"></span>
                    LIVE
                </span>
                <span style="font-size: 0.85rem; color: #666; margin-left: 0.5rem;">
                    Auto-updates every 30 seconds
                </span>
            </div>
        </div>

        <!-- Main Status Card -->
        <div class="status-card <?= $statusClass ?>">
            <div class="icon"><?= $statusIcon ?></div>
            <h2><?= $statusTitle ?></h2>
            <p style="font-size: 1.2rem; margin-bottom: 0.5rem;"><?= $statusMessage ?></p>
            <p style="font-size: 0.95rem; color: #666;"><?= $statusTime ?></p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📅</div>
                <h3><?= $latestEvent ? date('M j', strtotime($latestEvent['arrival_date'])) : 'N/A' ?></h3>
                <p>Last Water Supply</p>
            </div>
            <div class="stat-card">
                <div class="icon">🔄</div>
                <h3><?= $eventCount > 1 ? 'Every ' . round(30 / $eventCount, 1) . ' days' : 'N/A' ?></h3>
                <p>Average Frequency</p>
            </div>
            <div class="stat-card">
                <div class="icon">📊</div>
                <h3><?= $eventCount ?></h3>
                <p>Events (Last 30 Days)</p>
            </div>
        </div>

        <!-- Recent Events -->
        <?php if (!empty($recentEvents)): ?>
            <div class="card">
                <h2>Recent Water Arrivals</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentEvents as $event): ?>
                            <tr>
                                <td><?= date('M j, Y', strtotime($event['arrival_date'])) ?></td>
                                <td><?= date('l', strtotime($event['arrival_date'])) ?></td>
                                <td><?= date('g:i A', strtotime($event['arrival_time'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
            <a href="history.php" class="btn btn-primary">📊 View Full History</a>
            <a href="dashboard.php" class="btn btn-success">🔄 Refresh Now</a>
        </div>
    </div>

    <!-- Auto-refresh indicator -->
    <script>
        // Show when page will refresh
        let seconds = 30;
        setInterval(() => {
            seconds--;
            if (seconds <= 0) seconds = 30;
            document.title = `(${seconds}s) Dashboard - <?= SITE_NAME ?>`;
        }, 1000);
    </script>
</body>
</html>

