<?php
/**
 * ADMIN PANEL - Live Water Flow Control
 * Auto-refreshes every 30 seconds
 */
require_once 'config.php';
requireAdmin();

$success = '';
$error = '';

// Handle water flow toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_water'])) {
    $location_id = intval($_POST['location_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    
    if ($location_id > 0 && in_array($new_status, ['flowing', 'not_flowing'])) {
        // Get location name
        $stmt = $conn->prepare("SELECT location_name FROM locations WHERE id = ?");
        $stmt->bind_param('i', $location_id);
        $stmt->execute();
        $locData = $stmt->get_result()->fetch_assoc();
        $location_name = $locData['location_name'] ?? 'Unknown';
        
        // Update water status
        $stmt = $conn->prepare("UPDATE locations SET water_status = ?, status_updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('si', $new_status, $location_id);
        $stmt->execute();
        
        if ($new_status === 'flowing') {
            // Create water event (if not created in last hour)
            $stmt = $conn->prepare("
                SELECT id FROM water_events 
                WHERE location_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->bind_param('i', $location_id);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows === 0) {
                // Create event
                $stmt = $conn->prepare("
                    INSERT INTO water_events (location_id, arrival_date, arrival_time, admin_id) 
                    VALUES (?, CURDATE(), CURTIME(), 1)
                ");
                $stmt->bind_param('i', $location_id);
                $stmt->execute();
                
                // Count users (no emails sent)
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE location_id = ?");
                $stmt->bind_param('i', $location_id);
                $stmt->execute();
                $userCount = $stmt->get_result()->fetch_assoc()['count'];
                
                $success = "Water flow started in {$location_name}! ({$userCount} users notified)";
            } else {
                $success = "Water status updated for {$location_name}";
            }
        } else {
            $success = "Water flow stopped in {$location_name}";
        }
    }
}

// Get all locations with status
$locations = [];
$result = $conn->query("
    SELECT 
        l.*,
        COUNT(DISTINCT u.id) as user_count,
        (SELECT COUNT(*) FROM water_events we WHERE we.location_id = l.id AND DATE(we.created_at) = CURDATE()) as events_today
    FROM locations l
    LEFT JOIN users u ON l.id = u.location_id
    GROUP BY l.id
    ORDER BY l.district ASC, l.location_name ASC
");

while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

// Group by district
$byDistrict = [];
foreach ($locations as $loc) {
    $district = $loc['district'] ?? 'Other';
    if (!isset($byDistrict[$district])) {
        $byDistrict[$district] = [];
    }
    $byDistrict[$district][] = $loc;
}

// Get statistics
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalLocations = count($locations);
$eventsToday = $conn->query("SELECT COUNT(*) as count FROM water_events WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
$flowingCount = $conn->query("SELECT COUNT(*) as count FROM locations WHERE water_status = 'flowing'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="30"><!-- Auto-refresh every 30 seconds -->
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" style="background: #0A2A2E;">
        <div class="container">
            <a href="admin-panel.php" class="navbar-brand">🔒 Admin Panel</a>
            <div class="navbar-links">
                <span style="color: white;">👤 <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="text-center" style="margin: 2rem 0;">
            <h1>Water Arrival Control Panel</h1>
            <p style="font-size: 1.1rem; color: #666;">Manage water flow across all locations</p>
            <div style="margin-top: 1rem;">
                <span class="live-badge" style="background: #FEF3C7; color: #92400E;">
                    <span class="pulse-dot" style="background: #F59E0B;"></span>
                    LIVE
                </span>
                <span style="font-size: 0.85rem; color: #666; margin-left: 0.5rem;">
                    Auto-updates every 30 seconds
                </span>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">👥</div>
                <h3><?= $totalUsers ?></h3>
                <p>Registered Users</p>
            </div>
            <div class="stat-card">
                <div class="icon">📍</div>
                <h3><?= $totalLocations ?></h3>
                <p>Total Locations</p>
            </div>
            <div class="stat-card">
                <div class="icon">💧</div>
                <h3><?= $flowingCount ?></h3>
                <p>Locations Flowing</p>
            </div>
            <div class="stat-card">
                <div class="icon">📅</div>
                <h3><?= $eventsToday ?></h3>
                <p>Events Today</p>
            </div>
        </div>

        <!-- Water Flow Control -->
        <div class="card">
            <h2 style="margin-bottom: 1.5rem;">🌊 Live Water Flow Control</h2>
            <p style="color: #666; margin-bottom: 2rem;">
                Toggle water flow for each location. When turned ON, it creates a history event. 
                When turned OFF, status is updated only.
            </p>

            <!-- Summary -->
            <div style="background: #E0F2FE; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
                <strong>Summary:</strong>
                <span style="color: #10B981; font-weight: bold;"><?= $flowingCount ?></span> locations flowing,
                <span style="color: #EF4444; font-weight: bold;"><?= $totalLocations - $flowingCount ?></span> not flowing
            </div>

            <!-- Locations by District -->
            <?php foreach ($byDistrict as $district => $districtLocations): ?>
                <h3 style="color: #256A73; margin: 2rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #E0F2FE;">
                    <?= htmlspecialchars($district) ?>
                </h3>
                
                <div class="locations-grid">
                    <?php foreach ($districtLocations as $loc): ?>
                        <div class="location-card <?= $loc['water_status'] ?>">
                            <h3><?= htmlspecialchars($loc['location_name']) ?></h3>
                            <p class="location-meta"><?= htmlspecialchars($loc['zone'] ?? '') ?></p>
                            
                            <div style="display: flex; gap: 1rem; font-size: 0.85rem; color: #666; margin-bottom: 1rem;">
                                <span>👥 <?= $loc['user_count'] ?> users</span>
                                <span>📅 <?= $loc['events_today'] ?> today</span>
                            </div>
                            
                            <div class="location-status <?= $loc['water_status'] ?>">
                                <?= $loc['water_status'] === 'flowing' ? '💧 Water Flowing' : '❌ No Water' ?>
                            </div>
                            
                            <?php if ($loc['status_updated_at']): ?>
                                <p style="font-size: 0.75rem; color: #666; margin: 0.5rem 0;">
                                    Updated: <?= date('M j, g:i A', strtotime($loc['status_updated_at'])) ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Toggle Form -->
                            <form method="POST" action="admin-panel.php" style="margin-top: 1rem;">
                                <input type="hidden" name="location_id" value="<?= $loc['id'] ?>">
                                <input type="hidden" name="new_status" value="<?= $loc['water_status'] === 'flowing' ? 'not_flowing' : 'flowing' ?>">
                                
                                <?php if ($loc['water_status'] === 'flowing'): ?>
                                    <button type="submit" name="toggle_water" class="btn btn-danger btn-block">
                                        Turn OFF
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="toggle_water" class="btn btn-success btn-block" 
                                            onclick="return confirm('Start water flow in <?= htmlspecialchars($loc['location_name']) ?>?\n\nThis will notify <?= $loc['user_count'] ?> user<?= $loc['user_count'] != 1 ? 's' : '' ?>.');">
                                        Turn ON
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Auto-refresh countdown -->
    <script>
        let seconds = 30;
        setInterval(() => {
            seconds--;
            if (seconds <= 0) seconds = 30;
            document.title = `(${seconds}s) Admin Panel - <?= SITE_NAME ?>`;
        }, 1000);
    </script>
</body>
</html>

