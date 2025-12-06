<?php
/**
 * DATABASE CONNECTION TEST
 * This file tests if the database connection is working properly
 * Access: http://localhost/GoodDream/test_db_connection.php
 */

require_once 'php/config.php';

echo "<!DOCTYPE html>\n";
echo "<html lang='en'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>Database Connection Test</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }\n";
echo "        .success { color: #10B981; background: #D1FAE5; padding: 15px; border-radius: 8px; margin: 10px 0; }\n";
echo "        .error { color: #EF4444; background: #FEE2E2; padding: 15px; border-radius: 8px; margin: 10px 0; }\n";
echo "        .info { background: #E0F2FE; padding: 15px; border-radius: 8px; margin: 10px 0; }\n";
echo "        h1 { color: #256A73; }\n";
echo "        table { width: 100%; border-collapse: collapse; margin: 20px 0; }\n";
echo "        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }\n";
echo "        th { background: #256A73; color: white; }\n";
echo "        .warning { color: #F59E0B; background: #FEF3C7; padding: 15px; border-radius: 8px; margin: 10px 0; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <h1>🧪 Melamchi Water Alert - Database Test</h1>\n";

// Test 1: Database Connection
echo "<h2>1. Database Connection</h2>\n";
if ($conn->connect_error) {
    echo "<div class='error'>❌ Connection Failed: " . $conn->connect_error . "</div>\n";
    echo "</body></html>";
    exit;
} else {
    echo "<div class='success'>✅ Database connection successful!</div>\n";
}

// Test 2: Check if all tables exist
echo "<h2>2. Database Tables</h2>\n";
$expected_tables = ['locations', 'users', 'admins', 'water_events'];
$result = $conn->query("SHOW TABLES");
$existing_tables = [];
while ($row = $result->fetch_array()) {
    $existing_tables[] = $row[0];
}

echo "<table>\n";
echo "<tr><th>Table Name</th><th>Status</th></tr>\n";
foreach ($expected_tables as $table) {
    $exists = in_array($table, $existing_tables);
    $status = $exists ? "✅ Exists" : "❌ Missing";
    $class = $exists ? "success" : "error";
    echo "<tr><td>$table</td><td class='$class'>$status</td></tr>\n";
}
echo "</table>\n";

// Test 3: Check data counts
echo "<h2>3. Data Summary</h2>\n";
echo "<table>\n";
echo "<tr><th>Table</th><th>Record Count</th></tr>\n";

foreach ($expected_tables as $table) {
    if (in_array($table, $existing_tables)) {
        $result = $conn->query("SELECT COUNT(*) as count FROM $table");
        $row = $result->fetch_assoc();
        echo "<tr><td>$table</td><td>" . $row['count'] . " records</td></tr>\n";
    }
}
echo "</table>\n";

// Test 4: Check admin user
echo "<h2>4. Admin User Check</h2>\n";
$result = $conn->query("SELECT id, username FROM admins LIMIT 5");
if ($result->num_rows > 0) {
    echo "<div class='success'>✅ Admin users found</div>\n";
    echo "<table>\n";
    echo "<tr><th>ID</th><th>Username</th></tr>\n";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['id'] . "</td><td>" . $row['username'] . "</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<div class='error'>❌ No admin users found. Run database/schema.sql</div>\n";
}

// Test 5: Check locations
echo "<h2>5. Locations Check</h2>\n";
$result = $conn->query("SELECT COUNT(*) as count FROM locations");
$row = $result->fetch_assoc();
$location_count = $row['count'];

if ($location_count > 0) {
    echo "<div class='success'>✅ $location_count locations loaded</div>\n";
    
    // Show first 10 locations
    $result = $conn->query("SELECT id, location_name, district FROM locations ORDER BY location_name LIMIT 10");
    echo "<table>\n";
    echo "<tr><th>ID</th><th>Location</th><th>District</th></tr>\n";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['id'] . "</td><td>" . $row['location_name'] . "</td><td>" . $row['district'] . "</td></tr>\n";
    }
    echo "</table>\n";
    if ($location_count > 10) {
        echo "<p><em>... and " . ($location_count - 10) . " more locations</em></p>\n";
    }
} else {
    echo "<div class='error'>❌ No locations found. Run database/schema.sql</div>\n";
}

// Test 6: Configuration Check
echo "<h2>6. Configuration Check</h2>\n";
echo "<div class='info'>\n";
echo "<strong>Database Name:</strong> " . DB_NAME . "<br>\n";
echo "<strong>Database Host:</strong> " . DB_HOST . "<br>\n";
echo "<strong>Site URL:</strong> " . SITE_URL . "<br>\n";
echo "<strong>Site Name:</strong> " . SITE_NAME . "<br>\n";
echo "</div>\n";

// Test 7: PHP Version & Extensions
echo "<h2>7. PHP Environment</h2>\n";
echo "<div class='info'>\n";
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>\n";
echo "<strong>MySQLi Extension:</strong> " . (extension_loaded('mysqli') ? '✅ Loaded' : '❌ Not loaded') . "<br>\n";
echo "<strong>Session Support:</strong> " . (function_exists('session_start') ? '✅ Available' : '❌ Not available') . "<br>\n";
echo "</div>\n";

// Final Status
echo "<h2>🎉 Test Complete!</h2>\n";
$all_tables_exist = count(array_diff($expected_tables, $existing_tables)) === 0;
if ($all_tables_exist && $location_count > 0) {
    echo "<div class='success'>\n";
    echo "<h3>✅ All tests passed!</h3>\n";
    echo "<p>Your database is properly configured and ready to use.</p>\n";
    echo "<p><strong>Next steps:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>Go to <a href='index.html'>Homepage</a></li>\n";
    echo "<li>Go to <a href='register.html'>Register</a> a new user</li>\n";
    echo "<li>Go to <a href='admin-login.html'>Admin Login</a> (username: admin, password: admin123)</li>\n";
    echo "</ul>\n";
    echo "</div>\n";
} else {
    echo "<div class='warning'>\n";
    echo "<h3>⚠️ Setup incomplete</h3>\n";
    echo "<p>Please import the database schema:</p>\n";
    echo "<ol>\n";
    echo "<li>Open phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>\n";
    echo "<li>Click 'Import' tab</li>\n";
    echo "<li>Choose file: database/schema.sql</li>\n";
    echo "<li>Click 'Go'</li>\n";
    echo "<li>Refresh this page</li>\n";
    echo "</ol>\n";
    echo "</div>\n";
}

echo "<hr>\n";
echo "<p><em>Test file: test_db_connection.php | You can delete this file after testing</em></p>\n";
echo "</body>\n";
echo "</html>\n";

$conn->close();
?>

