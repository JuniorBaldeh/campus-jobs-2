<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize variables
$username = $email = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Sanitize inputs
    $username = trim(filter_input(POST, 'username', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // Only proceed if no validation errors
    if (empty($errors)) {
        // DATABASE AUTHENTICATION - REPLACE WITH YOUR ACTUAL CODE
        $valid_user = false;
        $db_username = 'Junior'; // Example - get from database
        $db_email = 'yaya.jr@example.com'; // Example - get from database
        $db_password_hash = password_hash('correct_password', PASSWORD_DEFAULT); // Example hash

        // Check if credentials match
        if ($username === $db_username && $email === $db_email) {
            if (password_verify($password, $db_password_hash)) {
                $valid_user = true;
            }
        }

        if ($valid_user) {
            // Successful login
            $_SESSION['user_id'] = 1; // Use actual user ID from DB
            $_SESSION['user_name'] = $username;
            $_SESSION['user_email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Invalid credentials";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Jobs | Login</title>
    <style>
        body {
            background: linear-gradient(to left, #954695, #EB2D2D);
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            position: relative;
        }

        .header {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #FFFFFF;
            font-size: 24px;
            font-weight: bold;
        }

        .login-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 90%;
            max-width: 400px;
        }

        .login-form {
            background: #FFFFFF;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .error-message {
            color: #d9534f;
            background: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #954695;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #7a3a7a;
        }

        h2 {
            color: white;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">Campus Jobs</div>
    <div class="login-container">
        <h2>Login</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form class="login-form" action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required
                    value="<?php echo htmlspecialchars($username); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="login" class="login-btn">Login</button>
        </form>
    </div>
</body>

</html>