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
$username = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim(htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'));
    $password = $_POST['password'] ?? '';

    if (empty($username)) {
        $errors[] = "Username is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // Only proceed if no validation errors
    if (empty($errors)) {
        // DATABASE AUTHENTICATION 
        $valid_user = false;
        $db_username = 'Junior'; // get from database
        $db_password_hash = password_hash('correct_password', PASSWORD_DEFAULT);
        $db_username2 = 'Akeel';
        $db_password_hash2 = password_hash('correct_password', PASSWORD_DEFAULT);
        $db_username3 = 'Muhktar';
        $db_password_hash3 = password_hash('correct_password', PASSWORD_DEFAULT);

        // Check if credentials match any user
        if ($username === $db_username && password_verify($password, $db_password_hash)) {
            $valid_user = true;
        } elseif ($username === $db_username2 && password_verify($password, $db_password_hash2)) {
            $valid_user = true;
        } elseif ($username === $db_username3 && password_verify($password, $db_password_hash3)) {
            $valid_user = true;
        }

        if ($valid_user) {
            // Successful login
            $_SESSION['user_id'] = 1; // Use actual user ID from DB
            $_SESSION['user_name'] = $username;
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
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="login" class="login-btn">Login</button>
        </form>
    </div>
</body>

</html>