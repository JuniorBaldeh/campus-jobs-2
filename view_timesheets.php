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

    // Create tables if they don't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        is_manager BOOLEAN DEFAULT FALSE
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS timesheets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        week_start_date DATE NOT NULL,
        hours_worked DECIMAL(5,2) NOT NULL,
        status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft',
        submitted_at DATETIME,
        approved_at DATETIME,
        approved_by INT,
        FOREIGN KEY (employee_id) REFERENCES employees(id),
        FOREIGN KEY (approved_by) REFERENCES employees(id)
    )");
    
    // Insert sample data if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM employees");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO employees (name, email, is_manager) VALUES 
            ('John Doe', 'john@example.com', FALSE),
            ('Jane Smith', 'jane@example.com', TRUE)");
        
        $pdo->exec("INSERT INTO timesheets (employee_id, week_start_date, hours_worked, status, submitted_at) VALUES 
            (1, '2023-10-02', 40.0, 'submitted', NOW()),
            (1, '2023-10-09', 35.5, 'draft', NULL)");
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle CRUD operations
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_timesheet'])) {
        try {
            $data = [
                'employee_id' => $_POST['employee_id'],
                'week_start_date' => $_POST['week_start_date'],
                'hours_worked' => $_POST['hours_worked'],
                'status' => $_POST['status'] ?? 'draft'
            ];

            if (empty($_POST['id'])) {
                // Create new timesheet
                $stmt = $pdo->prepare("INSERT INTO timesheets 
                    (employee_id, week_start_date, hours_worked, status) 
                    VALUES (:employee_id, :week_start_date, :hours_worked, :status)");
                $stmt->execute($data);
                $message = "Timesheet successfully added!";
            } else {
                // Update existing timesheet
                $data['id'] = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE timesheets SET 
                    employee_id = :employee_id, 
                    week_start_date = :week_start_date, 
                    hours_worked = :hours_worked,
                    status = :status
                    WHERE id = :id");
                $stmt->execute($data);
                $message = "Timesheet successfully updated!";
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['approve_timesheet']) && isset($_POST['timesheet_id'])) {
        try {
            $managerId = 2; // In real app, use logged-in manager's ID
            $stmt = $pdo->prepare("UPDATE timesheets 
                SET status = 'approved', 
                    approved_at = NOW(), 
                    approved_by = ?
                WHERE id = ? AND status = 'submitted'");
            $stmt->execute([$managerId, $_POST['timesheet_id']]);
            $message = "Timesheet approved successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM timesheets WHERE id = ? AND status = 'draft'");
            $stmt->execute([$_GET['id']]);
            $message = "Timesheet successfully deleted!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } elseif ($_GET['action'] === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM timesheets WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $editTimesheet = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($_GET['action'] === 'submit' && isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("UPDATE timesheets 
                SET status = 'submitted', 
                    submitted_at = NOW()
                WHERE id = ? AND status = 'draft'");
            $stmt->execute([$_GET['id']]);
            $message = "Timesheet submitted for approval!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timesheet Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #6a1b9a;
            --light-bg: #f8f9fa;
            --white: #ffffff;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--primary);
        }

        .timesheet-card {
            transition: all 0.2s ease;
            border-left: 4px solid var(--primary);
        }

        .timesheet-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .text-primary {
            color: var(--primary) !important;
        }

        .badge-status {
            background-color: #f3e5f5;
            color: var(--primary);
        }
        
        .badge-draft { background-color: #e0e0e0; color: #333; }
        .badge-submitted { background-color: #fff3e0; color: #e65100; }
        .badge-approved { background-color: #e8f5e9; color: #2e7d32; }
        .badge-rejected { background-color: #ffebee; color: #c62828; }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-calendar-check me-2"></i>Timesheet Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?action=create"><i class="bi bi-plus-circle me-1"></i>New Timesheet</a>
                    </li>
                    <?php if (!empty($pendingApprovals)): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#approvals">
                                <i class="bi bi-clipboard-check me-1"></i>Pending Approvals
                                <span class="badge bg-light text-dark ms-1"><?php echo count($pendingApprovals); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['action']) && in_array($_GET['action'], ['create', 'edit'])): ?>
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0"><?php echo isset($editTimesheet) ? 'Edit Timesheet' : 'Create New Timesheet'; ?></h2>
                </div>
                <div class="card-body">
                    <form method="post">
                        <?php if (isset($editTimesheet)): ?>
                            <input type="hidden" name="id" value="<?php echo $editTimesheet['id']; ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee*</label>
                                <select class="form-select" name="employee_id" required>
                                    <?php foreach ($employees as $employee): ?>
                                        <option value="<?php echo $employee['id']; ?>"
                                            <?php echo (isset($editTimesheet) && $editTimesheet['employee_id'] == $employee['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($employee['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Week Start Date*</label>
                                <input type="date" class="form-control" name="week_start_date" required
                                    value="<?php echo isset($editTimesheet) ? htmlspecialchars($editTimesheet['week_start_date']) : ''; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Hours Worked*</label>
                                <input type="number" step="0.5" class="form-control" name="hours_worked" required
                                    value="<?php echo isset($editTimesheet) ? htmlspecialchars($editTimesheet['hours_worked']) : ''; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="draft" <?php echo (isset($editTimesheet) && $editTimesheet['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="submitted" <?php echo (isset($editTimesheet) && $editTimesheet['status'] === 'submitted') ? 'selected' : ''; ?>>Submitted</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <button type="submit" name="save_timesheet" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i><?php echo isset($editTimesheet) ? 'Update' : 'Save'; ?>
                                </button>
                                <a href="?" class="btn btn-outline-secondary">Cancel</a>
                                
                                <?php if (isset($editTimesheet) && $editTimesheet['status'] === 'draft'): ?>
                                    <a href="?action=submit&id=<?php echo $editTimesheet['id']; ?>" class="btn btn-success ms-2">
                                        <i class="bi bi-send me-1"></i>Submit for Approval
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($pendingApprovals)): ?>
            <div id="approvals" class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Timesheets Pending Approval</h2>
                    <span class="badge bg-primary"><?php echo count($pendingApprovals); ?> pending</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Week</th>
                                    <th>Hours</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingApprovals as $ts): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ts['employee_name']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($ts['week_start_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($ts['hours_worked']); ?></td>
                                        <td><?php echo date('M j, Y g:i a', strtotime($ts['submitted_at'])); ?></td>
                                        <td>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="timesheet_id" value="<?php echo $ts['id']; ?>">
                                                <button type="submit" name="approve_timesheet" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                            </form>
                                            <a href="?action=edit&id=<?php echo $ts['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">All Timesheets</h2>
                <a href="?action=create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add New
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($timesheets)): ?>
                    <div class="alert alert-info">
                        No timesheets found. Click "Add New" to create your first timesheet.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Week</th>
                                    <th>Hours</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($timesheets as $ts): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ts['employee_name']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($ts['week_start_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($ts['hours_worked']); ?></td>
                                        <td>
                                            <?php 
                                                $badgeClass = 'badge-' . $ts['status'];
                                                echo '<span class="badge ' . $badgeClass . '">' . 
                                                    ucfirst($ts['status']) . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <a href="?action=edit&id=<?php echo $ts['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <?php if ($ts['status'] === 'draft'): ?>
                                                <a href="?action=delete&id=<?php echo $ts['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to delete this timesheet?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                bootstrap.Alert.getInstance(alert)?.close();
            });
        }, 5000);
    </script>
</body>
</html>