<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Start session and check admin login
session_start();

// Simple authentication - in production, use a more secure method
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle actions (mark as read, delete, etc.)
    if (isset($_GET['action'])) {
        $id = (int) $_GET['id'];

        switch ($_GET['action']) {
            case 'mark_read':
                $stmt = $pdo->prepare("UPDATE contact_submissions SET status = 'read' WHERE id = ?");
                $stmt->execute([$id]);
                break;

            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM contact_submissions WHERE id = ?");
                $stmt->execute([$id]);
                break;

            case 'mark_replied':
                $stmt = $pdo->prepare("UPDATE contact_submissions SET status = 'replied' WHERE id = ?");
                $stmt->execute([$id]);
                break;
        }

        header("Location: contact_admin.php");
        exit;
    }

    // Get all submissions
    $stmt = $pdo->query("SELECT * FROM contact_submissions ORDER BY submission_date DESC");
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submissions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr.new {
            background-color: #fff8e1;
        }

        tr.read {
            background-color: #e8f5e9;
        }

        tr.replied {
            background-color: #e3f2fd;
        }

        tr.spam {
            background-color: #ffebee;
        }

        .status-new {
            color: #ff8f00;
        }

        .status-read {
            color: #2e7d32;
        }

        .status-replied {
            color: #1565c0;
        }

        .status-spam {
            color: #c62828;
        }

        .actions a {
            margin-right: 10px;
        }

        .logout {
            float: right;
        }
    </style>
</head>

<body>
    <h1>Contact Form Submissions <a href="?logout=1" class="logout">Logout</a></h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $submission): ?>
                <tr class="<?= $submission['status'] ?>">
                    <td><?= $submission['id'] ?></td>
                    <td><?= date('M j, Y H:i', strtotime($submission['submission_date'])) ?></td>
                    <td><?= htmlspecialchars($submission['name']) ?></td>
                    <td><a
                            href="mailto:<?= htmlspecialchars($submission['email']) ?>"><?= htmlspecialchars($submission['email']) ?></a>
                    </td>
                    <td><?= htmlspecialchars($submission['subject']) ?></td>
                    <td class="status-<?= $submission['status'] ?>"><?= ucfirst($submission['status']) ?></td>
                    <td class="actions">
                        <a href="view_submission.php?id=<?= $submission['id'] ?>">View</a>
                        <a href="?action=mark_read&id=<?= $submission['id'] ?>">Mark Read</a>
                        <a href="?action=mark_replied&id=<?= $submission['id'] ?>">Mark Replied</a>
                        <a href="?action=delete&id=<?= $submission['id'] ?>"
                            onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>