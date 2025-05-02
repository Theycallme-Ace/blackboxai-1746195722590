<?php
require_once '../config.php';

// Mock function to simulate external GPS API call
function fetch_gps_data($gps_unit_id) {
    // In real implementation, call the external API with $gps_unit_id and parse response
    // Here we simulate with random coordinates for demo
    return [
        'latitude' =>  -6.200000 + (mt_rand() / mt_getrandmax()) * 0.1,  // Jakarta approx
        'longitude' => 106.816666 + (mt_rand() / mt_getrandmax()) * 0.1,
    ];
}

// Fetch vehicles with gps_unit_id
$stmt = $pdo->query("SELECT id, gps_unit_id FROM vehicles WHERE gps_unit_id IS NOT NULL AND gps_unit_id != ''");
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($vehicles as $vehicle) {
    $gps_data = fetch_gps_data($vehicle['gps_unit_id']);
    if ($gps_data) {
        // Check if gps_data exists for vehicle
        $stmt_check = $pdo->prepare("SELECT id FROM gps_data WHERE vehicle_id = ?");
        $stmt_check->execute([$vehicle['id']]);
        $existing = $stmt_check->fetchColumn();

        if ($existing) {
            // Update
            $stmt_update = $pdo->prepare("UPDATE gps_data SET latitude = ?, longitude = ?, last_updated = NOW() WHERE vehicle_id = ?");
            $stmt_update->execute([$gps_data['latitude'], $gps_data['longitude'], $vehicle['id']]);
        } else {
            // Insert
            $stmt_insert = $pdo->prepare("INSERT INTO gps_data (vehicle_id, latitude, longitude) VALUES (?, ?, ?)");
            $stmt_insert->execute([$vehicle['id'], $gps_data['latitude'], $gps_data['longitude']]);
        }
    }
}

echo "GPS data updated for " . count($vehicles) . " vehicles.\n";
?>
