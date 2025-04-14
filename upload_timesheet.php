<?php
// Database configuration
$host = 'localhost';
$dbname = 'campusjobs';
$username = 'root';
$password = '';

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle all CRUD operations
$message = '';
$upload_dir = 'uploads/';

// Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE operation
    if (isset($_POST['submit_timesheet'])) {
        try {
            // $student_name = htmlspecialchars(trim($_POST['student_name']));
            $week_start = $_POST['week_start'];
            $hours_worked = (float) $_POST['hours_worked'];
            $file_path = null;

            // Validate inputs
            if (empty($student_name) || empty($week_start) || empty($hours_worked)) {
                throw new Exception("All fields are required");
            }

            // Handle file upload
            if (isset($_FILES['timesheet_file']) && $_FILES['timesheet_file']['error'] === UPLOAD_ERR_OK) {
                $allowed_extensions = ['pdf', 'xls', 'xlsx', 'doc', 'docx'];
                $file_extension = strtolower(pathinfo($_FILES['timesheet_file']['name'], PATHINFO_EXTENSION));

                if (!in_array($file_extension, $allowed_extensions)) {
                    throw new Exception("Only PDF, Excel, and Word files are allowed");
                }

                $file_name = uniqid() . '_' . basename($_FILES['timesheet_file']['name']);
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['timesheet_file']['tmp_name'], $target_file)) {
                    $file_path = $target_file;
                } else {
                    throw new Exception("Failed to upload file");
                }
            }

            $stmt = $pdo->prepare("INSERT INTO timesheets (student_name, week_start, hours_worked, file_path) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_name, $week_start, $hours_worked, $file_path]);
            $message = "Timesheet submitted successfully!";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }

    // UPDATE operation
    if (isset($_POST['update_timesheet'])) {
        try {
            $id = (int) $_POST['id'];
            $student_name = htmlspecialchars(trim($_POST['student_name']));
            $week_start = $_POST['week_start'];
            $hours_worked = (float) $_POST['hours_worked'];

            $stmt = $pdo->prepare("UPDATE timesheets SET student_name=?, week_start=?, hours_worked=? WHERE id=?");
            $stmt->execute([$student_name, $week_start, $hours_worked, $id]);
            $message = "Timesheet updated successfully!";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// DELETE operation
if (isset($_GET['delete'])) {
    try {
        $id = (int) $_GET['delete'];

        // First get file path to delete the file
        $stmt = $pdo->prepare("SELECT file_path FROM timesheets WHERE id=?");
        $stmt->execute([$id]);
        $file_path = $stmt->fetchColumn();

        if ($file_path && file_exists($file_path)) {
            unlink($file_path);
        }

        $stmt = $pdo->prepare("DELETE FROM timesheets WHERE id=?");
        $stmt->execute([$id]);
        $message = "Timesheet deleted successfully!";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Get all timesheets for display
// $stmt = $pdo->query("SELECT * FROM timesheets ORDER BY week_start DESC");
// $timesheets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single timesheet for editing
$edit_timesheet = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM timesheets WHERE id=?");
    $stmt->execute([$id]);
    $edit_timesheet = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Timesheet System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .table-responsive {
            margin-top: 30px;
        }

        .file-link {
            color: #0d6efd;
            text-decoration: none;
        }

        .file-link:hover {
            text-decoration: underline;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 class="text-center mb-4">Timesheet System</h2>
            <a href="recruiter_dashboard.php" class="btn btn-cancel">Back to Dashboard</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo strpos($message, 'Error') === false ? 'success' : 'danger'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h4><?php echo $edit_timesheet ? 'Edit Timesheet' : 'Add New Student Timesheet'; ?></h4>
            <form method="post" enctype="multipart/form-data">
                <?php if ($edit_timesheet): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_timesheet['id']; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="student_name" class="form-label">Student Name</label>
                    <input type="text" class="form-control" id="student_name" name="student_name"
                        value="<?php echo $edit_timesheet ? htmlspecialchars($edit_timesheet['student_name']) : ''; ?>"
                        required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="week_start" class="form-label">Week Starting</label>
                        <input type="date" class="form-control" id="week_start" name="week_start"
                            value="<?php echo $edit_timesheet ? $edit_timesheet['week_start'] : ''; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="hours_worked" class="form-label">Hours Worked</label>
                        <input type="number" step="0.01" class="form-control" id="hours_worked" name="hours_worked"
                            value="<?php echo $edit_timesheet ? $edit_timesheet['hours_worked'] : ''; ?>" required>
                    </div>
                </div>


        </div>

        <button type="submit" name="<?php echo $edit_timesheet ? 'update_timesheet' : 'submit_timesheet'; ?>"
            class="btn btn-primary">
            <?php echo $edit_timesheet ? 'Update Timesheet' : 'Submit Timesheet'; ?>
        </button>

        <?php if ($edit_timesheet): ?>
            <a href="upload_timesheet.php" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <h4>Timesheet Records</h4>
        <?php if (!empty($timesheets)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Week Starting</th>
                        <th>Hours</th>
                        <th>File</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($timesheets as $ts): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ts['student_name']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($ts['week_start'])); ?></td>
                            <td><?php echo htmlspecialchars($ts['hours_worked']); ?></td>
                            <td>
                                <?php if (!empty($ts['file_path'])): ?>
                                    <a href="<?php echo $ts['file_path']; ?>" target="_blank" class="file-link">
                                        Download
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No file</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M j, Y g:i a', strtotime($ts['uploaded_at'])); ?></td>
                            <td>
                                <a href="upload_timesheet.php?edit=<?php echo $ts['id']; ?>"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <a href="upload_timesheet.php?delete=<?php echo $ts['id']; ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this timesheet?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No timesheets found.</p>
        <?php endif; ?>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>