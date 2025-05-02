<?php
require_once '../functions.php';

if (!is_logged_in() || !has_role('staff')) {
    header('Location: login.php');
    exit;
}

require_once '../config.php';

$error = '';
$success = '';

// Handle add vehicle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle'])) {
    $type = $_POST['type'] ?? '';
    $name = $_POST['name'] ?? '';
    $year = $_POST['year'] ?? '';
    $description = $_POST['description'] ?? '';
    $gps_unit_id = $_POST['gps_unit_id'] ?? '';
    $facilities = $_POST['facilities'] ?? [];
    $photos = $_FILES['photos'] ?? null;

    if (!$type || !$name || !$year) {
        $error = 'Tipe, Nama, dan Tahun kendaraan wajib diisi.';
    } else {
        // Insert vehicle
        $stmt = $pdo->prepare("INSERT INTO vehicles (type, name, year, description, gps_unit_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$type, $name, $year, $description, $gps_unit_id]);
        $vehicle_id = $pdo->lastInsertId();

        // Handle facilities
        if (!empty($facilities)) {
            $stmt = $pdo->prepare("INSERT INTO vehicle_facilities (vehicle_id, facility_id) VALUES (?, ?)");
            foreach ($facilities as $facility_id) {
                $stmt->execute([$vehicle_id, $facility_id]);
            }
        }

        // Handle photo upload
        if ($photos && isset($photos['name']) && is_array($photos['name'])) {
            $upload_dir = __DIR__ . '/../uploads/vehicles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $stmt = $pdo->prepare("INSERT INTO vehicle_photos (vehicle_id, photo_path) VALUES (?, ?)");
            for ($i = 0; $i < count($photos['name']); $i++) {
                if ($photos['error'][$i] === UPLOAD_ERR_OK) {
                    $tmp_name = $photos['tmp_name'][$i];
                    $filename = basename($photos['name'][$i]);
                    $target_file = $upload_dir . uniqid() . '_' . $filename;
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $relative_path = 'uploads/vehicles/' . basename($target_file);
                        $stmt->execute([$vehicle_id, $relative_path]);
                    }
                }
            }
        }

        $success = 'Kendaraan berhasil ditambahkan.';
    }
}

// Fetch vehicles list
$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY created_at DESC");
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Vehicles - Admin - Rental Bis & Car</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-semibold">Manage Vehicles</h1>
        <a href="dashboard.php" class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300 transition">Back to Dashboard</a>
    </header>
    <main class="container mx-auto p-4">
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <section class="mb-8">
            <h2 class="text-lg font-bold mb-4">Add New Vehicle</h2>
            <form method="POST" action="" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
                <input type="hidden" name="add_vehicle" value="1" />
                <label class="block mb-2 font-semibold" for="type">Type</label>
                <select id="type" name="type" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    <option value="">Select Type</option>
                    <option value="bus">Bus</option>
                    <option value="car">Car</option>
                </select>

                <label class="block mb-2 font-semibold" for="name">Name</label>
                <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded mb-4" required />

                <label class="block mb-2 font-semibold" for="year">Year</label>
                <input type="number" id="year" name="year" min="1900" max="<?= date('Y') ?>" class="w-full p-2 border border-gray-300 rounded mb-4" required />

                <label class="block mb-2 font-semibold" for="gps_unit_id">GPS Unit ID</label>
                <input type="text" id="gps_unit_id" name="gps_unit_id" class="w-full p-2 border border-gray-300 rounded mb-4" />

                <label class="block mb-2 font-semibold" for="description">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full p-2 border border-gray-300 rounded mb-4"></textarea>

                <label class="block mb-2 font-semibold" for="facilities">Facilities</label>
                <div class="mb-4 border p-2 rounded max-h-40 overflow-y-auto bg-gray-50">
                    <?php foreach ($facilities as $facility): ?>
                        <label class="inline-flex items-center mr-4 mb-2">
                            <input type="checkbox" name="facilities[]" value="<?= htmlspecialchars($facility['id']) ?>" class="form-checkbox" />
                            <span class="ml-2"><?= htmlspecialchars($facility['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <label class="block mb-2 font-semibold" for="photos">Photos</label>
                <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="mb-6" />

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Add Vehicle</button>
            </form>
        </section>

        <section>
            <h2 class="text-lg font-bold mb-4">Vehicles List</h2>
            <table class="w-full bg-white rounded shadow">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Type</th>
                        <th class="p-2 border">Name</th>
                        <th class="p-2 border">Year</th>
                        <th class="p-2 border">GPS Unit ID</th>
                        <th class="p-2 border">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $v): ?>
                        <tr>
                            <td class="p-2 border"><?= htmlspecialchars($v['id']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($v['type']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($v['name']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($v['year']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($v['gps_unit_id']) ?></td>
                            <td class="p-2 border"><?= nl2br(htmlspecialchars($v['description'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($vehicles)): ?>
                        <tr><td colspan="6" class="p-2 border text-center">No vehicles found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
