<?php
/**
 * WATER SUPPLY HISTORY
 * Shows complete history for user's location
 */
require_once 'config.php';
requireLogin();

$location_id = $_SESSION['user_location_id'];
$location_name = $_SESSION['user_location_name'];

// Get filter
$days = $_GET['days'] ?? 30;
if ($days === 'all') {
    $days = null;
} else {
    $days = intval($days);
}

// Get history events
if ($days) {
    $stmt = $conn->prepare("
        SELECT * FROM water_events 
        WHERE location_id = ? 
        AND arrival_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ORDER BY arrival_date DESC, arrival_time DESC
    ");
    $stmt->bind_param('ii', $location_id, $days);
} else {
    $stmt = $conn->prepare("
        SELECT * FROM water_events 
        WHERE location_id = ? 
        ORDER BY arrival_date DESC, arrival_time DESC
    ");
    $stmt->bind_param('i', $location_id);
}

$stmt->execute();
$result = $stmt->get_result();
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$totalEvents = count($events);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="60"><!-- Auto-refresh every 60 seconds -->
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
            <h1>Water Supply History</h1>
            <p style="font-size: 1.1rem; color: #666;">
                Complete record for <strong><?= htmlspecialchars($location_name) ?></strong>
            </p>
            <div style="margin-top: 1rem;">
                <span class="live-badge">
                    <span class="pulse-dot"></span>
                    LIVE
                </span>
                <span style="font-size: 0.85rem; color: #666; margin-left: 0.5rem;">
                    Auto-updates every 60 seconds
                </span>
            </div>
        </div>

        <!-- Filter -->
        <div class="card">
            <form method="GET" action="history.php" style="display: flex; gap: 1rem; align-items: center;">
                <label style="font-weight: 600;">Filter:</label>
                <select name="days" onchange="this.form.submit()">
                    <option value="7" <?= $days == 7 ? 'selected' : '' ?>>Last 7 Days</option>
                    <option value="30" <?= $days == 30 ? 'selected' : '' ?>>Last 30 Days</option>
                    <option value="90" <?= $days == 90 ? 'selected' : '' ?>>Last 90 Days</option>
                    <option value="all" <?= $days === null ? 'selected' : '' ?>>All Time</option>
                </select>
                <span style="margin-left: auto; color: #666;">
                    Total: <strong><?= $totalEvents ?></strong> events
                </span>
            </form>
        </div>

        <!-- History Table -->
        <?php if (!empty($events)): ?>
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Days Ago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $index => $event): ?>
                            <?php
                            $date = strtotime($event['arrival_date']);
                            $daysAgo = floor((time() - $date) / 86400);
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= date('M j, Y', $date) ?></td>
                                <td><?= date('l', $date) ?></td>
                                <td><?= date('g:i A', strtotime($event['arrival_time'])) ?></td>
                                <td><?= $daysAgo == 0 ? 'Today' : $daysAgo . ' day' . ($daysAgo != 1 ? 's' : '') . ' ago' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="card text-center">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📭</div>
                <h2>No Water Events Yet</h2>
                <p style="color: #666;">No water arrivals recorded for your area in the selected period.</p>
            </div>
        <?php endif; ?>

        <div class="text-center" style="margin-top: 2rem;">
            <a href="dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
        </div>
    </div>

    <!-- Auto-refresh countdown -->
    <script>
        let seconds = 60;
        setInterval(() => {
            seconds--;
            if (seconds <= 0) seconds = 60;
            document.title = `(${seconds}s) History - <?= SITE_NAME ?>`;
        }, 1000);
    </script>
</body>
</html>

