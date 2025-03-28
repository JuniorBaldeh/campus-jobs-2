<?php
// Start session and verify authentication
session_start();

// Redirect to login if not authenticated as recruiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit();
}

// Get recruiter data from session
$companyName = $_SESSION['company'] ?? 'Your Company';
$userName = $_SESSION['user_name'] ?? 'Recruiter';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #954695;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .welcome-message {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .logout-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Recruiter Dashboard</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($userName); ?>
                (<?php echo htmlspecialchars($companyName); ?>)</span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome-message">
            <h2>Recruiter Tools</h2>
            <p>You are logged in as a recruiter for <?php echo htmlspecialchars($companyName); ?>.</p>
        </div>

        <div>
            <h3>Recruiter Actions</h3>
            <ul>
                <li><a href="post_job.php">Post New Job</a></li>
                <li><a href="view_applications.php">View Applications</a></li>
                <li><a href="manage_jobs.php">Manage Job Listings</a></li>
                <li><a href="students_timeshe.php">Student Timesheets Listings</a></li>
            </ul>
        </div>
    </div>
</body>

</html>