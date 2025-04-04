<?php
// Start session and verify authentication
session_start();

// Redirect to login if not authenticated as recruiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get recruiter data from session
$companyName = $_SESSION['company'] ?? 'Your Company';
$userName = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #954695;
            --danger: #dc3545;
            --success: #28a745;
            --dark: #343a40;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f4f4f4;
        }

        nav {
            background: var(--dark);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            padding: 5px 10px;
        }

        .logout-btn {
            background: var(--danger);
            padding: 8px 15px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary);
        }

        .btn-success {
            background: var(--success);
        }

        .btn-danger {
            background: var(--danger);
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
            <!-- <span style="margin-right:15px;">Welcome, <?= htmlspecialchars($admin['first_name']) ?></span> -->
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2>Admin Controls</h2>
            <div class="quick-actions">
                <a href="add_student.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Student</a>
                <a href="review_timesheets.php" class="btn btn-primary"><i class="fas fa-file-alt"></i> Review
                    Timesheets</a>
            </div>
        </div>

        <div class="card">
            <h2>Recent Activity</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Action</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>John Doe</td>
                        <td>Timesheet Submission</td>
                        <td>2023-11-15 14:30</td>
                        <td><span class="btn btn-success">Approved</span></td>
                    </tr>
                    <tr>
                        <td>Jane Smith</td>
                        <td>Job Application</td>
                        <td>2023-11-15 10:15</td>
                        <td><span class="btn btn-danger">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Simple tab functionality (if needed)
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            document.getElementById(tabId).style.display = 'block';
        }

        // Confirm important actions
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (!confirm('Are you sure?')) e.preventDefault();
            });
        });
    </script>
</body>

</html>