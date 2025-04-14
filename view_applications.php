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

    // Create jobs table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS jobs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        company VARCHAR(255) NOT NULL,
        location VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        posted_date DATE NOT NULL,
        salary VARCHAR(100),
        job_type VARCHAR(50)
    )");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle CRUD operations
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_job'])) {
        try {
            $data = [
                'title' => $_POST['title'],
                'company' => $_POST['company'],
                'location' => $_POST['location'],
                'description' => $_POST['description'],
                'salary' => $_POST['salary'] ?? null,
                'job_type' => $_POST['job_type'] ?? null,
                'posted_date' => date('Y-m-d')
            ];

            if (empty($_POST['id'])) {
                // Create new job
                $stmt = $pdo->prepare("INSERT INTO jobs (title, company, location, description, salary, job_type, posted_date) 
                                      VALUES (:title, :company, :location, :description, :salary, :job_type, :posted_date)");
                $stmt->execute($data);
                $message = "Job successfully added!";
            } else {
                // Update existing job
                $data['id'] = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE jobs SET 
                    title = :title, 
                    company = :company, 
                    location = :location, 
                    description = :description,
                    salary = :salary,
                    job_type = :job_type
                    WHERE id = :id");
                $stmt->execute($data);
                $message = "Job successfully updated!";
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $message = "Job successfully deleted!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } elseif ($_GET['action'] === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $editJob = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Get all jobs
$jobs = $pdo->query("SELECT * FROM jobs ORDER BY posted_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Simplified color scheme */
        :root {
            --primary: #954695;
            --light-bg: #f8f9fa;
            --white: #ffffff;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Navbar styling */
        .navbar {
            background-color: var(--primary);
        }

        /* Card styling */
        .job-card {
            transition: all 0.2s ease;
            border-left: 4px solid var(--primary);
        }

        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        /* Button styling */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* Utility classes */
        .text-primary {
            color: var(--primary) !important;
        }

        .badge-type {
            background-color: #f3e5f5;
            color: var(--primary);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-briefcase me-2"></i>Job Listings
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <!-- <a class="nav-link" href="?action=create"><i class="bi bi-plus-circle me-1"></i>Post Job</a> -->
                    </li>
                </ul>
                <a href="recruiter_dashboard.php" class="btn btn-cancel">Back to Dashboard</a>
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
                    <h2 class="h5 mb-0"><?php echo isset($editJob) ? 'Edit Job' : 'Post New Job'; ?></h2>
                </div>
                <div class="card-body">
                    <form method="post">
                        <?php if (isset($editJob)): ?>
                            <input type="hidden" name="id" value="<?php echo $editJob['id']; ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Job Title*</label>
                                <input type="text" class="form-control" name="title"
                                    value="<?php echo isset($editJob) ? htmlspecialchars($editJob['title']) : ''; ?>"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Company*</label>
                                <input type="text" class="form-control" name="company"
                                    value="<?php echo isset($editJob) ? htmlspecialchars($editJob['company']) : ''; ?>"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Location*</label>
                                <input type="text" class="form-control" name="location"
                                    value="<?php echo isset($editJob) ? htmlspecialchars($editJob['location']) : ''; ?>"
                                    required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Job Type</label>
                                <select class="form-select" name="job_type">
                                    <option value="">Select type</option>
                                    <option value="Full-time" <?php echo (isset($editJob) && $editJob['job_type'] === 'Full-time') ? 'selected' : ''; ?>>Full-time</option>
                                    <option value="Part-time" <?php echo (isset($editJob) && $editJob['job_type'] === 'Part-time') ? 'selected' : ''; ?>>Part-time</option>
                                    <option value="Contract" <?php echo (isset($editJob) && $editJob['job_type'] === 'Contract') ? 'selected' : ''; ?>>Contract</option>
                                    <option value="Internship" <?php echo (isset($editJob) && $editJob['job_type'] === 'Internship') ? 'selected' : ''; ?>>Internship</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Salary</label>
                                <input type="text" class="form-control" name="salary"
                                    value="<?php echo isset($editJob) ? htmlspecialchars($editJob['salary']) : ''; ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description*</label>
                                <textarea class="form-control" name="description" rows="5" required><?php
                                echo isset($editJob) ? htmlspecialchars($editJob['description']) : '';
                                ?></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" name="save_job" class="btn btn-primary">
                                    <i
                                        class="bi bi-save me-1"></i><?php echo isset($editJob) ? 'Update Job' : 'Save Job'; ?>
                                </button>
                                <a href="?" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Current Job Openings</h2>
                <a href="?action=create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add New
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($jobs)): ?>
                    <div class="alert alert-info">
                        No job listings found. Click "Add New" to post your first job.
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($jobs as $job): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="job-card card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span
                                                class="badge badge-type rounded-pill"><?php echo htmlspecialchars($job['job_type'] ?? 'N/A'); ?></span>
                                            <small
                                                class="text-muted"><?php echo date('M j, Y', strtotime($job['posted_date'])); ?></small>
                                        </div>
                                        <h3 class="h5"><?php echo htmlspecialchars($job['title']); ?></h3>
                                        <p class="text-primary mb-1"><?php echo htmlspecialchars($job['company']); ?></p>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($job['location']); ?>
                                            <?php if (!empty($job['salary'])): ?>
                                                | <i class="bi bi-cash"></i> <?php echo htmlspecialchars($job['salary']); ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white border-0 pt-0">
                                        <div class="d-flex justify-content-between">
                                            <a href="?action=edit&id=<?php echo $job['id']; ?>"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="?action=delete&id=<?php echo $job['id']; ?>"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this job?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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