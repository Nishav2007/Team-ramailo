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

// Get all users with their location details
$allUsers = [];
$usersResult = $conn->query("
    SELECT 
        u.id, 
        u.name, 
        u.email, 
        u.created_at,
        l.location_name,
        l.district,
        l.zone
    FROM users u
    JOIN locations l ON u.location_id = l.id
    ORDER BY u.created_at DESC
");
while ($row = $usersResult->fetch_assoc()) {
    $allUsers[] = $row;
}

// Get user count per location
$usersPerLocation = [];
$locationCountResult = $conn->query("
    SELECT 
        l.id,
        l.location_name,
        l.district,
        l.zone,
        COUNT(u.id) as user_count
    FROM locations l
    LEFT JOIN users u ON l.id = u.location_id
    GROUP BY l.id, l.location_name, l.district, l.zone
    HAVING user_count > 0
    ORDER BY user_count DESC, l.location_name ASC
");
while ($row = $locationCountResult->fetch_assoc()) {
    $usersPerLocation[] = $row;
}

// Get active tab from URL
$activeTab = $_GET['tab'] ?? 'water';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - GoodDream</title>
    <link rel="stylesheet" href="gooddream-theme.css">
    <meta http-equiv="refresh" content="30">
    <style>
        .tab-button {
            padding: 1rem 2rem;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            color: #666;
        }
        .tab-button.active {
            background: var(--gradient-1);
            color: white;
            border-radius: 10px 10px 0 0;
            border-bottom-color: var(--teal-primary);
        }
        .tab-button:hover:not(.active) {
            color: var(--teal-primary);
            background: rgba(20, 184, 166, 0.05);
        }
        .location-card {
            padding: 1.5rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            transition: all 0.3s;
        }
        .location-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(20, 184, 166, 0.15);
        }
        .location-card.flowing {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
        .location-card.not-flowing {
            border-color: #e5e7eb;
        }
        .location-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .location-status.flowing {
            background: #10b981;
            color: white;
        }
        .location-status.not-flowing {
            background: #ef4444;
            color: white;
        }
        .locations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" style="background: rgba(255, 255, 255, 0.98);">
        <div class="container">
            <a href="admin-panel.php" class="navbar-brand">GoodDream Admin</a>
            <div class="navbar-links">
                <span style="color: #666; font-weight: 500;">👤 <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                <a href="logout.php" style="color: #ef4444;">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="text-center" style="margin-bottom: 2rem;">
            <div class="css-icon icon-drop" style="margin: 0 auto 1.5rem;"></div>
            <h1 style="background: var(--gradient-1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Water Control Panel</h1>
            <p style="font-size: 1.1rem; color: #666;">Manage water flow across all locations</p>
            <div style="margin-top: 1rem;">
                <span class="live-badge" style="background: #fef3c7; color: #92400e;">
                    <span class="pulse-dot" style="background: #f59e0b;"></span>
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
                <div class="css-icon icon-user" style="margin: 0 auto 1rem; transform: scale(0.6);"></div>
                <h3><?= $totalUsers ?></h3>
                <p>Registered Users</p>
            </div>
            <div class="stat-card">
                <div class="css-icon icon-map" style="margin: 0 auto 1rem; transform: scale(0.6);"></div>
                <h3><?= $totalLocations ?></h3>
                <p>Total Locations</p>
            </div>
            <div class="stat-card">
                <div class="css-icon icon-drop" style="margin: 0 auto 1rem; transform: scale(0.6);"></div>
                <h3><?= $flowingCount ?></h3>
                <p>Locations Flowing</p>
            </div>
            <div class="stat-card">
                <div class="css-icon icon-chart" style="margin: 0 auto 1rem; transform: scale(0.6);"></div>
                <h3><?= $eventsToday ?></h3>
                <p>Events Today</p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div style="display: flex; gap: 1rem; margin: 2rem 0; border-bottom: 2px solid rgba(20, 184, 166, 0.1);">
            <button 
                onclick="showTab('water')" 
                id="tab-water"
                class="tab-button <?= $activeTab === 'water' ? 'active' : '' ?>"
            >
                💧 Water Flow Control
            </button>
            <button 
                onclick="showTab('users')" 
                id="tab-users"
                class="tab-button <?= $activeTab === 'users' ? 'active' : '' ?>"
            >
                👥 User Management
            </button>
        </div>

        <!-- Water Flow Control Tab -->
        <div id="water-tab" style="display: <?= $activeTab === 'water' ? 'block' : 'none' ?>;">
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

            <!-- Search Bar -->
            <div style="background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <label for="location-search" style="font-weight: 600; color: #256A73; white-space: nowrap;">
                        🔍 Search Locations:
                    </label>
                    <input 
                        type="text" 
                        id="location-search" 
                        placeholder="Type location name, district, or zone..." 
                        style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #E0F2FE; border-radius: 8px; font-size: 1rem; font-family: inherit;"
                        onkeyup="filterLocations(this.value)"
                    >
                    <button 
                        type="button" 
                        onclick="clearSearch()" 
                        style="padding: 0.75rem 1.5rem; background: #EF4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;"
                    >
                        Clear
                    </button>
                </div>
                <div id="search-results" style="margin-top: 0.75rem; font-size: 0.9rem; color: #666;">
                    <span id="result-count">Showing all <?= $totalLocations ?> locations</span>
                </div>
            </div>

            <!-- Locations by District -->
            <div id="locations-container">
                <?php foreach ($byDistrict as $district => $districtLocations): ?>
                    <div class="district-section" data-district="<?= htmlspecialchars(strtolower($district)) ?>">
                        <h3 class="district-header" style="color: #256A73; margin: 2rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #E0F2FE;">
                            <?= htmlspecialchars($district) ?>
                        </h3>
                        
                        <div class="locations-grid">
                            <?php foreach ($districtLocations as $loc): ?>
                                <div class="location-card <?= $loc['water_status'] ?>" 
                                     data-location-name="<?= htmlspecialchars(strtolower($loc['location_name'])) ?>"
                                     data-district="<?= htmlspecialchars(strtolower($loc['district'] ?? '')) ?>"
                                     data-zone="<?= htmlspecialchars(strtolower($loc['zone'] ?? '')) ?>">
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
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- No Results Message -->
            <div id="no-results" class="card text-center" style="display: none; margin-top: 2rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🔍</div>
                <h2>No Locations Found</h2>
                <p style="color: #666;">Try searching with a different term or <a href="javascript:clearSearch()" style="color: #256A73; font-weight: 600;">clear the search</a>.</p>
            </div>
        </div>
        </div>

        <!-- User Management Tab -->
        <div id="users-tab" style="display: <?= $activeTab === 'users' ? 'block' : 'none' ?>;">
            <div class="card">
                <h2 style="margin-bottom: 1.5rem;">👥 User Management</h2>
                <p style="color: #666; margin-bottom: 2rem;">
                    View all registered users and their location distribution
                </p>

                <!-- User Count by Location -->
                <div style="margin-bottom: 3rem;">
                    <h3 style="color: #256A73; margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid #E0F2FE;">
                        📊 Users by Location (<?= count($usersPerLocation) ?> locations with users)
                    </h3>
                    
                    <?php if (!empty($usersPerLocation)): ?>
                        <div class="locations-grid">
                            <?php foreach ($usersPerLocation as $locData): ?>
                                <div class="location-card" style="text-align: center;">
                                    <h3 style="color: #256A73; margin-bottom: 0.5rem;"><?= htmlspecialchars($locData['location_name']) ?></h3>
                                    <p class="location-meta"><?= htmlspecialchars($locData['district']) ?> - <?= htmlspecialchars($locData['zone']) ?></p>
                                    <div style="font-size: 2.5rem; font-weight: bold; color: #256A73; margin: 1rem 0;">
                                        <?= $locData['user_count'] ?>
                                    </div>
                                    <p style="color: #666; font-size: 0.9rem;">
                                        user<?= $locData['user_count'] != 1 ? 's' : '' ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center" style="padding: 2rem; color: #666;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">👤</div>
                            <p>No users registered yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- All Users Table -->
                <div>
                    <h3 style="color: #256A73; margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid #E0F2FE;">
                        📋 All Registered Users (<?= count($allUsers) ?>)
                    </h3>
                    
                    <!-- User Search Bar -->
                    <?php if (!empty($allUsers)): ?>
                        <div style="background: #F9FAFB; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <label for="user-search" style="font-weight: 600; color: #256A73; white-space: nowrap;">
                                    🔍 Search Users:
                                </label>
                                <input 
                                    type="text" 
                                    id="user-search" 
                                    placeholder="Search by name, email, or location..." 
                                    style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #E0F2FE; border-radius: 8px; font-size: 1rem; font-family: inherit;"
                                    onkeyup="filterUsers(this.value)"
                                >
                                <button 
                                    type="button" 
                                    onclick="clearUserSearch()" 
                                    style="padding: 0.75rem 1.5rem; background: #EF4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;"
                                >
                                    Clear
                                </button>
                            </div>
                            <div style="margin-top: 0.75rem; font-size: 0.9rem; color: #666;">
                                <span id="user-result-count">Showing all <?= count($allUsers) ?> users</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($allUsers)): ?>
                        <div style="overflow-x: auto;">
                            <table id="users-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>District</th>
                                        <th>Zone</th>
                                        <th>Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allUsers as $index => $user): ?>
                                        <tr class="user-row" 
                                            data-name="<?= htmlspecialchars(strtolower($user['name'])) ?>"
                                            data-email="<?= htmlspecialchars(strtolower($user['email'])) ?>"
                                            data-location="<?= htmlspecialchars(strtolower($user['location_name'])) ?>"
                                            data-district="<?= htmlspecialchars(strtolower($user['district'])) ?>"
                                            data-zone="<?= htmlspecialchars(strtolower($user['zone'])) ?>">
                                            <td><?= $index + 1 ?></td>
                                            <td style="font-weight: 600;"><?= htmlspecialchars($user['name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><?= htmlspecialchars($user['location_name']) ?></td>
                                            <td><?= htmlspecialchars($user['district']) ?></td>
                                            <td><?= htmlspecialchars($user['zone']) ?></td>
                                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- No Users Found Message -->
                        <div id="no-users-results" class="text-center" style="display: none; padding: 2rem; color: #666;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">🔍</div>
                            <p>No users found matching your search.</p>
                        </div>
                    <?php else: ?>
                        <div class="text-center" style="padding: 2rem; color: #666;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">👤</div>
                            <p>No users registered yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh countdown and Search Functionality -->
    <script>
        let seconds = 30;
        setInterval(() => {
            seconds--;
            if (seconds <= 0) seconds = 30;
            document.title = `(${seconds}s) Admin Panel - <?= SITE_NAME ?>`;
        }, 1000);

        // Search functionality
        function filterLocations(searchTerm) {
            const search = searchTerm.toLowerCase().trim();
            const locationCards = document.querySelectorAll('.location-card');
            const districtSections = document.querySelectorAll('.district-section');
            const noResults = document.getElementById('no-results');
            const resultCount = document.getElementById('result-count');
            
            let visibleCount = 0;
            let hasVisibleLocations = false;

            // If search is empty, show all
            if (search === '') {
                locationCards.forEach(card => {
                    card.style.display = '';
                    visibleCount++;
                });
                districtSections.forEach(section => {
                    section.style.display = '';
                });
                noResults.style.display = 'none';
                resultCount.textContent = `Showing all <?= $totalLocations ?> locations`;
                return;
            }

            // Filter location cards
            locationCards.forEach(card => {
                const locationName = card.getAttribute('data-location-name') || '';
                const district = card.getAttribute('data-district') || '';
                const zone = card.getAttribute('data-zone') || '';
                
                const matches = locationName.includes(search) || 
                               district.includes(search) || 
                               zone.includes(search);
                
                if (matches) {
                    card.style.display = '';
                    visibleCount++;
                    hasVisibleLocations = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide district sections based on visible locations
            districtSections.forEach(section => {
                const cardsInSection = section.querySelectorAll('.location-card');
                const visibleCards = Array.from(cardsInSection).filter(card => 
                    card.style.display !== 'none'
                );
                
                if (visibleCards.length > 0) {
                    section.style.display = '';
                    // Show district header
                    const header = section.querySelector('.district-header');
                    if (header) {
                        header.style.display = '';
                    }
                } else {
                    section.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (hasVisibleLocations) {
                noResults.style.display = 'none';
                resultCount.textContent = `Found ${visibleCount} location${visibleCount !== 1 ? 's' : ''} matching "${searchTerm}"`;
            } else {
                noResults.style.display = '';
                resultCount.textContent = `No locations found matching "${searchTerm}"`;
            }
        }

        // Clear search function
        function clearSearch() {
            const searchInput = document.getElementById('location-search');
            searchInput.value = '';
            filterLocations('');
            searchInput.focus();
        }

        // Tab switching
        function showTab(tabName) {
            // Hide all tabs
            document.getElementById('water-tab').style.display = 'none';
            document.getElementById('users-tab').style.display = 'none';
            
            // Remove active class from all buttons
            document.getElementById('tab-water').classList.remove('active');
            document.getElementById('tab-users').classList.remove('active');
            
            // Show selected tab
            if (tabName === 'water') {
                document.getElementById('water-tab').style.display = 'block';
                document.getElementById('tab-water').classList.add('active');
            } else if (tabName === 'users') {
                document.getElementById('users-tab').style.display = 'block';
                document.getElementById('tab-users').classList.add('active');
            }
        }

        // User search functionality
        function filterUsers(searchTerm) {
            const search = searchTerm.toLowerCase().trim();
            const userRows = document.querySelectorAll('.user-row');
            const noResults = document.getElementById('no-users-results');
            const resultCount = document.getElementById('user-result-count');
            const usersTable = document.getElementById('users-table');
            
            let visibleCount = 0;
            let hasVisibleUsers = false;

            // If search is empty, show all
            if (search === '') {
                userRows.forEach(row => {
                    row.style.display = '';
                    visibleCount++;
                });
                if (noResults) noResults.style.display = 'none';
                if (usersTable) usersTable.style.display = '';
                if (resultCount) resultCount.textContent = `Showing all <?= count($allUsers) ?> users`;
                return;
            }

            // Filter user rows
            userRows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const email = row.getAttribute('data-email') || '';
                const location = row.getAttribute('data-location') || '';
                const district = row.getAttribute('data-district') || '';
                const zone = row.getAttribute('data-zone') || '';
                
                const matches = name.includes(search) || 
                               email.includes(search) || 
                               location.includes(search) ||
                               district.includes(search) ||
                               zone.includes(search);
                
                if (matches) {
                    row.style.display = '';
                    visibleCount++;
                    hasVisibleUsers = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide table and no results message
            if (hasVisibleUsers) {
                if (noResults) noResults.style.display = 'none';
                if (usersTable) usersTable.style.display = '';
                if (resultCount) resultCount.textContent = `Found ${visibleCount} user${visibleCount !== 1 ? 's' : ''} matching "${searchTerm}"`;
            } else {
                if (noResults) noResults.style.display = '';
                if (usersTable) usersTable.style.display = 'none';
                if (resultCount) resultCount.textContent = `No users found matching "${searchTerm}"`;
            }
        }

        // Clear user search function
        function clearUserSearch() {
            const searchInput = document.getElementById('user-search');
            if (searchInput) {
                searchInput.value = '';
                filterUsers('');
                searchInput.focus();
            }
        }
    </script>
</body>
</html>

