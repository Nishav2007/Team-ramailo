<?php
/**
 * GET LOCATIONS
 * Returns all available locations for dropdown
 */

require_once 'config.php';

// Get all locations ordered by name
$query = "SELECT id, location_name, district, zone FROM locations ORDER BY location_name ASC";
$result = $conn->query($query);

$locations = [];
while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

sendJsonResponse([
    'success' => true,
    'locations' => $locations,
    'count' => count($locations)
]);
?>

