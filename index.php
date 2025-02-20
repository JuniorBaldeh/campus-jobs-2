<?php
// db.php - Database Connection
$host = 'localhost';
$db = 'timesheet_system';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if they don't exist
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    role ENUM('admin', 'recruiter', 'student'),
    email VARCHAR(255),
    password VARCHAR(255),
    visa_status ENUM('visa', 'non-visa'),
    weekly_hours_limit INT DEFAULT 15
);");

$conn->query("CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    requested_hours INT,
    status ENUM('pending', 'approved', 'denied'),
    FOREIGN KEY (student_id) REFERENCES users(id)
);");

$conn->query("CREATE TABLE IF NOT EXISTS timesheets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    worked_hours INT,
    status ENUM('pending', 'approved', 'flagged'),
    FOREIGN KEY (student_id) REFERENCES users(id)
);");

// login.php - Handle user login
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Timesheet System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        label,
        input,
        button {
            display: block;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <button type="submit" name="login">Login</button>
    </form>
</body>

</html>