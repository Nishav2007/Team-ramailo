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
    <title>History - GoodDream</title>
    <link rel="stylesheet" href="gooddream-theme.css">
    <meta http-equiv="refresh" content="60">
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
            <div class="css-icon icon-chart" style="margin: 0 auto 1.5rem;"></div>
            <h1 style="background: var(--gradient-1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Water Supply History</h1>
            <p style="font-size: 1.1rem; color: #666;">
                Complete record for <strong style="color: var(--teal-primary);"><?= htmlspecialchars($location_name) ?></strong>
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
            <form method="GET" action="history.php" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <label style="font-weight: 600; color: var(--teal-dark);">Filter by period:</label>
                <select name="days" onchange="this.form.submit()" style="padding: 0.75rem 1rem; border: 2px solid rgba(20, 184, 166, 0.2); border-radius: 10px; background: #f9fafb; font-size: 1rem; cursor: pointer; transition: all 0.3s;">
                    <option value="7" <?= $days == 7 ? 'selected' : '' ?>>Last 7 Days</option>
                    <option value="30" <?= $days == 30 ? 'selected' : '' ?>>Last 30 Days</option>
                    <option value="90" <?= $days == 90 ? 'selected' : '' ?>>Last 90 Days</option>
                    <option value="all" <?= $days === null ? 'selected' : '' ?>>All Time</option>
                </select>
                <span style="margin-left: auto; color: #666; font-weight: 500;">
                    Total: <strong style="color: var(--teal-primary); font-size: 1.2rem;"><?= $totalEvents ?></strong> events
                </span>
            </form>
        </div>

        <!-- History Table -->
        <?php if (!empty($events)): ?>
            <div class="card">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--gradient-2);">
                            <th style="padding: 1rem; text-align: left; color: var(--teal-dark); border-radius: 10px 0 0 0;">#</th>
                            <th style="padding: 1rem; text-align: left; color: var(--teal-dark);">Date</th>
                            <th style="padding: 1rem; text-align: left; color: var(--teal-dark);">Day</th>
                            <th style="padding: 1rem; text-align: left; color: var(--teal-dark);">Time</th>
                            <th style="padding: 1rem; text-align: left; color: var(--teal-dark); border-radius: 0 10px 0 0;">Days Ago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $index => $event): ?>
                            <?php
                            $date = strtotime($event['arrival_date']);
                            $daysAgo = floor((time() - $date) / 86400);
                            ?>
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.3s;" class="history-row">
                                <td style="padding: 1rem; color: #666;"><?= $index + 1 ?></td>
                                <td style="padding: 1rem; font-weight: 600; color: var(--teal-dark);"><?= date('M j, Y', $date) ?></td>
                                <td style="padding: 1rem; color: #666;"><?= date('l', $date) ?></td>
                                <td style="padding: 1rem; font-weight: 600; color: var(--teal-primary);"><?= date('g:i A', strtotime($event['arrival_time'])) ?></td>
                                <td style="padding: 1rem; color: #666;">
                                    <?php if ($daysAgo == 0): ?>
                                        <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">Today</span>
                                    <?php else: ?>
                                        <?= $daysAgo ?> day<?= $daysAgo != 1 ? 's' : '' ?> ago
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="card text-center" style="padding: 4rem 2rem;">
                <div class="css-icon icon-wave" style="margin: 0 auto 2rem; opacity: 0.3;"></div>
                <h2 style="color: #666; margin-bottom: 1rem;">No Water Events Yet</h2>
                <p style="color: #999;">No water arrivals recorded for your area in the selected period.</p>
            </div>
        <?php endif; ?>

        <div class="text-center" style="margin-top: 2rem;">
            <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
        </div>
    </div>

    <!-- Auto-refresh countdown -->
    <script>
        let seconds = 60;
        setInterval(() => {
            seconds--;
            if (seconds <= 0) seconds = 60;
            document.title = `(${seconds}s) History - GoodDream`;
        }, 1000);

        // Hover effect for table rows
        document.querySelectorAll('.history-row').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.background = 'rgba(20, 184, 166, 0.05)';
                row.style.transform = 'scale(1.01)';
            });
            row.addEventListener('mouseleave', () => {
                row.style.background = 'transparent';
                row.style.transform = 'scale(1)';
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
