<?php
require_once '../functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login - Rental Bis & Car</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Admin Login</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label class="block mb-2 font-semibold" for="username">Username</label>
            <input class="w-full p-2 border border-gray-300 rounded mb-4" type="text" id="username" name="username" required />
            <label class="block mb-2 font-semibold" for="password">Password</label>
            <input class="w-full p-2 border border-gray-300 rounded mb-6" type="password" id="password" name="password" required />
            <button class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition" type="submit">Login</button>
        </form>
    </div>
</body>
</html>
