<?php
require_once '../functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$role = get_user_role();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard - Rental Bis & Car</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-semibold">Admin Dashboard</h1>
        <div>
            <span class="mr-4">Logged in as: <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span>
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">Logout</a>
        </div>
    </header>
    <main class="container mx-auto p-4">
        <h2 class="text-lg font-bold mb-4">Welcome to the Admin Panel</h2>
        <p>Here you can manage vehicles, users, and more.</p>
        <!-- TODO: Add navigation and management features -->
    </main>
</body>
</html>
