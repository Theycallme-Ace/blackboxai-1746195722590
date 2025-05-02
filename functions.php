<?php
session_start();

require_once 'config.php';

// User login function
function login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        return true;
    }
    return false;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Get current user role name
function get_user_role() {
    global $pdo;
    if (!is_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT name FROM roles WHERE id = ?");
    $stmt->execute([$_SESSION['role_id']]);
    $role = $stmt->fetchColumn();
    return $role ?: null;
}

// Check if user has a specific role or higher privilege
function has_role($required_role) {
    $roles_hierarchy = ['user' => 1, 'staff' => 2, 'manager' => 3, 'admin' => 4, 'Superadmin' => 5];
    $user_role = get_user_role();
    if (!$user_role) return false;
    return $roles_hierarchy[$user_role] >= $roles_hierarchy[$required_role];
}

// Logout function
function logout() {
    session_destroy();
    header('Location: admin/login.php');
    exit;
}
?>
