<?php
session_start();

// Check if user is logged in as recruiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'campusjobs';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // [Rest of your existing process_job.php code] 
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $requirements = filter_input(INPUT_POST, 'requirements', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $salary = filter_input(INPUT_POST, 'salary', FILTER_SANITIZE_STRING);
    $jobType = filter_input(INPUT_POST, 'job_type', FILTER_SANITIZE_STRING);
    $recruiterId = $_SESSION['user_id'];
    $companyName = $_SESSION['company'];

    // Basic validation
    if (empty($title) || empty($description) || empty($requirements) || empty($location) || empty($jobType)) {
        $_SESSION['error_message'] = "Please fill in all required fields";
        header("Location: jobs.php");
        exit();
    }

    // Insert job into database
    try {
        $stmt = $pdo->prepare("INSERT INTO jobs 
                              (recruiter_id, company, title, description, requirements, location, salary, job_type, posted_date) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([
            $recruiterId,
            $companyName,
            $title,
            $description,
            $requirements,
            $location,
            $salary,
            $jobType
        ]);

        $_SESSION['success_message'] = "Job posted successfully!";
        header("Location: manage_jobs.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error posting job: " . $e->getMessage();
        header("Location: jobs.php");
        exit();
    }
} else {
    // If someone tries to access this page directly without submitting the form
    header("Location: jobs.php");
    exit();
}

