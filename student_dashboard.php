<?php
// Start session and check authentication
session_start();

// Verify user is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* .nav-left {
            background-color: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        } */

        .nav-left,
        .nav-right {
            display: flex;
            /* gap: 20px; */
        }

        /* nav a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        } */

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .logout-button {
            background-color: #954695;
            padding: 8px 16px;
        }

        .logout-button:hover {
            background-color: #7a3a7a;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .welcome-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .content-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .content-section>div {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .hours-display {
            font-size: 24px;
            font-weight: bold;
            color: #954695;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <nav>
        <div class="nav-left">
            <!-- <a href="student_dashboard.html">Home</a> -->
            <a href="timesheets.html">Timesheets</a>
            <a href="notifications.html">Notifications</a>
            <a href="profile.html">Profile</a>
        </div>
        <div class="nav-right">
            <a href="Logout.php" class="logout-button">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($student['full_name'] ?? 'Student'); ?></h2>
            <p>Student ID: <?php echo htmlspecialchars($student['student_id'] ?? ''); ?></p>
        </div>

        <div class="content-section">
            <div>
                <h3>Real-Time Hour Tracking</h3>
                <p>Your remaining work hours for this week:</p>
                <div class="hours-display">
                 <!-- <?php echo htmlspecialchars($remaining_hours); ?>/ 20 hours remaining -->
                </div>
                <a href="timesheets.php" class="btn-primary">View All Timesheets</a>
            </div>

            <div>
                <h3>Partial Approval</h3>
                <p>If you are available for fewer hours than requested, you can negotiate adjustments with your
                    recruiter.</p>
                <a href="notifications.php" class="btn-primary">View Requests</a>
            </div>
        </div>

        <div class="recent-timesheets" style="margin-top: 30px;">
            <h3>Recent Timesheet Entries</h3>
            <!-- <?php if (count($recent_timesheets) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Recruiter</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Total Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_timesheets as $timesheet): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($timesheet['date']); ?></td>
                                <td><?php echo htmlspecialchars($timesheet['recruiter_name']); ?></td>
                                <td><?php echo htmlspecialchars($timesheet['time_in']); ?></td>
                                <td><?php echo htmlspecialchars($timesheet['time_out']); ?></td>
                                <td><?php echo htmlspecialchars($timesheet['total_hours']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($timesheet['status'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table> -->
            <?php else: ?>
                <p>No timesheet entries found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>