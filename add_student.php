<?php
// Database Connection (at the very top)
$host = '127.0.0.1';
$dbname = 'campusjobs';
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Rest of your admin authentication and CRUD logic...
session_start();

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ... rest of your existing add_students.php code ...

// Initialize variables
$error = '';
$success = '';
$student = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'password' => '',
    'visa_restricted' => 0
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize inputs
        $student = [
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'visa_restricted' => isset($_POST['visa_restricted']) ? 1 : 0
        ];

        // Basic validation
        if (empty($student['first_name']) || empty($student['last_name']) || empty($student['email'])) {
            throw new Exception("All fields are required");
        }

        if (!filter_var($student['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if email exists
        $stmt = $conn->prepare("SELECT student_id FROM Students WHERE email = ?");
        $stmt->execute([$student['email']]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email already exists");
        }

        // Insert new student
        $stmt = $conn->prepare("
            INSERT INTO Students 
            (first_name, last_name, email, password, visa_restricted, role) 
            VALUES (?, ?, ?, ?, ?, 'student')
        ");
        $stmt->execute([
            $student['first_name'],
            $student['last_name'],
            $student['email'],
            $student['password'],
            $student['visa_restricted']
        ]);

        $success = "Student added successfully!";
        $student = []; // Clear form on success
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    // After the student is successfully added ($stmt->execute)
if ($stmt->rowCount() > 0) {
    // Record this activity
    $conn->query("
        INSERT INTO activities 
        (student_id, action_type, status) 
        VALUES (
            LAST_INSERT_ID(), 
            'student_added', 
            'approved'
        )
    ");
    
    $success = "Student added successfully!";
    $student = []; // Clear form
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #6a11cb;
            --primary-light: #8e2de2;
            --secondary: #2575fc;
            --success: #4BB543;
            --warning: #FFA500;
            --danger: #FF3333;
            --dark: #2C3E50;
            --light: #F5F7FA;
            --text: #333333;
            --text-light: #7F8C8D;
            --border: #e0e0e0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: var(--text);
            line-height: 1.6;
        }

        nav {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-links a:hover, .nav-links a.active {
            border-bottom-color: white;
        }

        .nav-links i {
            margin-right: 8px;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .card {
            max-width: 700px;
            margin: 2rem auto;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            background: white;
            border: none;
        }

        .card h2 {
            color: var(--dark);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 1.8rem;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #fafafa;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.1);
            outline: none;
            background-color: white;
        }

        .form-check {
            margin: 2rem 0;
            display: flex;
            align-items: center;
        }

        .form-check input[type="checkbox"] {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 5px;
            margin-right: 12px;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        .form-check input[type="checkbox"]:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check input[type="checkbox"]:checked::after {
            content: "✓";
            position: absolute;
            color: white;
            font-size: 12px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .form-check label {
            margin-bottom: 0;
            cursor: pointer;
            user-select: none;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(106, 17, 203, 0.2);
        }

        .btn-secondary {
            background: white;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: #f5f5f5;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert i {
            font-size: 1.2rem;
        }

        .alert-danger {
            background-color: #fff0f0;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .alert-success {
            background-color: #f0fff4;
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        @media (max-width: 768px) {
            .card {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 12px;
            }
            
            .btn-secondary {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-links">
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_students.php"><i class="fas fa-users"></i> Students</a>
            <a href="manage_timesheets.php"><i class="fas fa-clock"></i> Timesheets</a>
        </div>
        <div>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2><i class="fas fa-user-plus"></i> Add New Student</h2>
            
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?= isset($student['first_name']) ? htmlspecialchars($student['first_name']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?= isset($student['last_name']) ? htmlspecialchars($student['last_name']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?= isset($student['email']) ? htmlspecialchars($student['email']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-check">
                    <input type="checkbox" id="visa_restricted" name="visa_restricted" 
                           <?= isset($student['visa_restricted']) && $student['visa_restricted'] ? 'checked' : '' ?>>
                    <label for="visa_restricted">Visa Restricted (15hr/week limit)</label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Student
                </button>
                
                <a href="manage_students.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Students
                </a>
            </form>
        </div>
    </div>
</body>
</html>