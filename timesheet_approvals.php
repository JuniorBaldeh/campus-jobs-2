<?php
session_start(); // Must be at the very top

// Initialize variables
$timesheets = []; // Or fetch from database

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch timesheets data (example using PDO)
try {
    $db = new PDO('mysql:host=localhost;dbname=your_db', 'username', 'password');
    $stmt = $db->prepare("SELECT * FROM timesheets WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $timesheets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error
    $timesheets = [];
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Timesheets</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .approved {
            color: green;
        }

        .rejected {
            color: red;
        }

        .pending {
            color: orange;
        }
    </style>
</head>

<body>
    <h2>My Timesheets</h2>
    <table>
        <tr>
            <th>Week Starting</th>
            <th>Hours</th>
            <th>Tasks</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($timesheets as $ts): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($ts['week_starting'])) ?></td>
                <td><?= $ts['hours_worked'] ?></td>
                <td><?= nl2br(htmlspecialchars($ts['tasks_completed'])) ?></td>
                <td class="<?= $ts['status'] ?>"><?= ucfirst($ts['status']) ?></td>
                <td>
                    <?php if ($ts['status'] == 'draft'): ?>
                        <a href="edit_timesheet.php?id=<?= $ts['timesheet_id'] ?>">Edit</a> |
                        <a href="delete_timesheet.php?id=<?= $ts['timesheet_id'] ?>"
                            onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php if ($_SESSION['role'] == 'supervisor'): ?>
        <h2>Timesheets Pending Approval</h2>
        <table>
            <tr>
                <th>Student</th>
                <th>Week Starting</th>
                <th>Hours</th>
                <th>Tasks</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($pending_approvals as $pa): ?>
                <tr>
                    <td><?= htmlspecialchars($pa['student_name']) ?></td>
                    <td><?= date('d/m/Y', strtotime($pa['week_starting'])) ?></td>
                    <td><?= $pa['hours_worked'] ?></td>
                    <td><?= nl2br(htmlspecialchars($pa['tasks_completed'])) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($pa['submission_date'])) ?></td>
                    <td>
                        <a href="approve_timesheet.php?id=<?= $pa['timesheet_id'] ?>">Approve</a> |
                        <a href="reject_timesheet.php?id=<?= $pa['timesheet_id'] ?>">Reject</a> |
                        <a href="view_timesheet.php?id=<?= $pa['timesheet_id'] ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>

</html>