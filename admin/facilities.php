<?php
require_once '../functions.php';

if (!is_logged_in() || !has_role('staff')) {
    header('Location: login.php');
    exit;
}

require_once '../config.php';

$error = '';
$success = '';

// Handle add facility form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_facility'])) {
    $name = trim($_POST['name'] ?? '');
    if (!$name) {
        $error = 'Nama fasilitas wajib diisi.';
    } else {
        // Check if facility exists
        $stmt = $pdo->prepare("SELECT id FROM facilities WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $error = 'Fasilitas sudah ada.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO facilities (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = 'Fasilitas berhasil ditambahkan.';
        }
    }
}

// Fetch facilities list
$stmt = $pdo->query("SELECT * FROM facilities ORDER BY name ASC");
$facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Facilities - Admin - Rental Bis & Car</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-semibold">Manage Facilities</h1>
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
            <h2 class="text-lg font-bold mb-4">Add New Facility</h2>
            <form method="POST" action="" class="bg-white p-4 rounded shadow">
                <input type="hidden" name="add_facility" value="1" />
                <label class="block mb-2 font-semibold" for="name">Facility Name</label>
                <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded mb-4" required />
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Add Facility</button>
            </form>
        </section>

        <section>
            <h2 class="text-lg font-bold mb-4">Facilities List</h2>
            <ul class="bg-white rounded shadow p-4 list-disc list-inside">
                <?php foreach ($facilities as $f): ?>
                    <li><?= htmlspecialchars($f['name']) ?></li>
                <?php endforeach; ?>
                <?php if (empty($facilities)): ?>
                    <li>No facilities found.</li>
                <?php endif; ?>
            </ul>
        </section>
    </main>
</body>
</html>
