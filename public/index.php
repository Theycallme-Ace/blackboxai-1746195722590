<?php
require_once '../config.php';

// Fetch vehicles to display with photos and facilities
$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY created_at DESC");
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch photos for vehicles
$vehicle_photos = [];
if ($vehicles) {
    $vehicle_ids = array_column($vehicles, 'id');
    $in_query = implode(',', array_fill(0, count($vehicle_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM vehicle_photos WHERE vehicle_id IN ($in_query)");
    $stmt->execute($vehicle_ids);
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($photos as $photo) {
        $vehicle_photos[$photo['vehicle_id']][] = $photo['photo_path'];
    }
}

// Fetch facilities for vehicles
$vehicle_facilities = [];
if ($vehicles) {
    $stmt = $pdo->prepare("SELECT vf.vehicle_id, f.name FROM vehicle_facilities vf JOIN facilities f ON vf.facility_id = f.id WHERE vf.vehicle_id IN ($in_query)");
    $stmt->execute($vehicle_ids);
    $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($facilities as $facility) {
        $vehicle_facilities[$facility['vehicle_id']][] = $facility['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Rental Bis & Car</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4 text-center text-2xl font-semibold">
        Rental Bis & Car
    </header>
    <main class="container mx-auto p-4">
        <h1 class="text-xl font-bold mb-4">Kendaraan Tersedia</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($vehicles as $vehicle): ?>
                <div class="bg-white rounded shadow p-4">
                    <h2 class="text-lg font-semibold mb-2"><?= htmlspecialchars($vehicle['name']) ?> (<?= htmlspecialchars($vehicle['type']) ?>)</h2>
                    <p>Tahun: <?= htmlspecialchars($vehicle['year']) ?></p>
                    <p class="mt-2"><?= nl2br(htmlspecialchars($vehicle['description'])) ?></p>
                    <?php if (!empty($vehicle_photos[$vehicle['id']])): ?>
                        <div class="mb-2">
                            <?php foreach ($vehicle_photos[$vehicle['id']] as $photo): ?>
                                <img src="<?= htmlspecialchars($photo) ?>" alt="Photo of <?= htmlspecialchars($vehicle['name']) ?>" class="w-full h-40 object-cover rounded mb-1" />
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($vehicle_facilities[$vehicle['id']])): ?>
                        <div class="mt-2">
                            <strong>Facilities:</strong>
                            <ul class="list-disc list-inside text-sm">
                                <?php foreach ($vehicle_facilities[$vehicle['id']] as $facility_name): ?>
                                    <li><?= htmlspecialchars($facility_name) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if (empty($vehicles)): ?>
                <p>Tidak ada kendaraan tersedia saat ini.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
