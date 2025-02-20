<?php
// login.php - Simple Authentication System without Database
session_start();

// Predefined users (Hardcoded for simplicity)
$users = [
    'admin' => ['password' => 'admin123', 'role' => 'admin'],
    'student' => ['password' => 'student123', 'role' => 'student']
];

// Handle login request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $users[$username]['role'];

        // Redirect based on role
        if ($_SESSION['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>