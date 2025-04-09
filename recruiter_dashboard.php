<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit();
}

$companyName = $_SESSION['company'];
$userName = $_SESSION['user_name'];
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
            line-height: 1.6;
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

        .logout-btn:hover {
            background-color: #c9302c;
        }

        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            flex: 1;
            min-width: 250px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #954695;
        }

        .card h3 {
            margin-top: 0;
            color: #954695;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .card ul {
            list-style: none;
            padding: 0;
        }

        .card li {
            padding: 8px 0;
            border-bottom: 1px solid #f4f4f4;
        }

        .card li:last-child {
            border-bottom: none;
        }

        .card a {
            color: #333;
            text-decoration: none;
            display: block;
            padding: 5px 0;
        }

        .card a:hover {
            color: #954695;
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

        <div class="dashboard-cards">
            <!-- Job Management Card -->
            <div class="card">
                <h3>Job Management</h3>
                <ul>
                    <li><a href="jobs.php">Post New Job</a></li>
                    <li><a href="manage_jobs.php">Manage Job Listings</a></li>
                    <li><a href="view_applications.php">View Applications</a></li>
                </ul>
            </div>

            <!-- Timesheets Card -->
            <div class="card">
                <h3>Timesheets</h3>
                <ul>
                    <li><a href="upload_timesheet.php">Upload Timesheets</a></li>
                    <li><a href="view_timesheets.php">View Timesheets</a></li>
                    <li><a href="timesheet_approvals.php">Approve Timesheets</a></li>
                    <li><a href="contact_admin.php?reason=timesheet_delay">Report Issue</a></li>
                </ul>
            </div>

            <!-- Profile Card -->
            <div class="card">
                <h3>My Profile</h3>
                <ul>
                    <li><a href="edit_profile.php">Update Profile</a></li>
                    <li><a href="change_password.php">Change Password</a></li>
                    <li><a href="notification_settings.php">Notification Settings</a></li>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>