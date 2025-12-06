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
$statusTitle = 'No Recent Water Supply';
$statusMessage = 'No water events recorded recently';
$statusTime = 'You will receive notifications when water arrives';

if ($waterStatus === 'flowing') {
    // PRIORITY 1: Water is flowing RIGHT NOW
    $statusClass = 'flowing';
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
    <title>Dashboard - GoodDream</title>
    <link rel="stylesheet" href="gooddream-theme.css">
    <meta http-equiv="refresh" content="30">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="navbar-brand">GoodDream</a>
            <div class="navbar-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="history.php">History</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="text-center" style="margin-bottom: 2rem;">
            <h1 style="background: var(--gradient-1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Welcome, <?= htmlspecialchars($user_name) ?>!</h1>
            <p style="font-size: 1.1rem; color: #666; margin-top: 0.5rem;">
                Water supply dashboard for <strong style="color: var(--teal-primary);"><?= htmlspecialchars($location_name) ?></strong>
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
            <div class="css-icon icon-drop" style="margin: 0 auto 1.5rem;"></div>
            <h2><?= $statusTitle ?></h2>
            <p style="font-size: 1.2rem; margin-bottom: 0.5rem;"><?= $statusMessage ?></p>
            <p style="font-size: 0.95rem; opacity: 0.8;"><?= $statusTime ?></p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="css-icon icon-chart" style="margin: 0 auto 1rem; transform: scale(0.6);"></div>
                <h3><?= $latestEvent ? date('M j', strtotime($latestEvent['arrival_date'])) : 'N/A' ?></h3>
                <p>Last Water Supply</p>
            </div>
            <div class="stat-card">
                <div class="css-icon icon-wave" style="margin: 0 auto 1rem; transform: scale(0.6);"></div>
                <h3><?= $eventCount > 1 ? 'Every ' . round(30 / $eventCount, 1) . ' days' : 'N/A' ?></h3>
                <p>Average Frequency</p>
            </div>
            <div class="stat-card">
                <div class="css-icon icon-map" style="margin: 0 auto 1rem; transform: scale(0.6);"></div>
                <h3><?= $eventCount ?></h3>
                <p>Events (Last 30 Days)</p>
            </div>
        </div>

        <!-- Recent Events -->
        <?php if (!empty($recentEvents)): ?>
            <div class="card">
                <h2 style="background: var(--gradient-1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Recent Water Arrivals</h2>
                <table style="width: 100%; border-collapse: collapse; margin-top: 1.5rem;">
                    <thead>
                        <tr style="background: var(--gradient-2); color: var(--teal-dark);">
                            <th style="padding: 1rem; text-align: left; border-radius: 10px 0 0 0;">Date</th>
                            <th style="padding: 1rem; text-align: left;">Day</th>
                            <th style="padding: 1rem; text-align: left; border-radius: 0 10px 0 0;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentEvents as $event): ?>
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.3s;">
                                <td style="padding: 1rem;"><?= date('M j, Y', strtotime($event['arrival_date'])) ?></td>
                                <td style="padding: 1rem;"><?= date('l', strtotime($event['arrival_date'])) ?></td>
                                <td style="padding: 1rem; font-weight: 600; color: var(--teal-primary);"><?= date('g:i A', strtotime($event['arrival_time'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap;">
            <a href="history.php" class="btn btn-primary">View Full History</a>
            <a href="dashboard.php" class="btn btn-outline">Refresh Now</a>
        </div>
    </div>

    <!-- Auto-refresh indicator -->
    <script>
        let seconds = 30;
        setInterval(() => {
            seconds--;
            if (seconds <= 0) seconds = 30;
            document.title = `(${seconds}s) Dashboard - GoodDream`;
        }, 1000);

        // Hover effect for table rows
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.background = 'rgba(20, 184, 166, 0.05)';
            });
            row.addEventListener('mouseleave', () => {
                row.style.background = 'transparent';
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 2px 20px rgba(20, 184, 166, 0.2)';
            } else {
                navbar.style.boxShadow = '0 2px 20px rgba(20, 184, 166, 0.1)';
            }
        });
    </script>
</body>
</html>
