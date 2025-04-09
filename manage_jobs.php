<?php
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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_job'])) {
        // Add new job
        $stmt = $pdo->prepare("INSERT INTO jobs (title, company, location, description, posted_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['company'],
            $_POST['location'],
            $_POST['description'],
            date('Y-m-d')
        ]);
        $message = "Job successfully added!";
    } elseif (isset($_POST['update_job'])) {
        // Update job
        $stmt = $pdo->prepare("UPDATE jobs SET title=?, company=?, location=?, description=? WHERE id=?");
        $stmt->execute([
            $_POST['title'],
            $_POST['company'],
            $_POST['location'],
            $_POST['description'],
            $_POST['id']
        ]);
        $message = "Job successfully updated!";
    }
} elseif (isset($_GET['delete_id'])) {
    // Delete job
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id=?");
    $stmt->execute([$_GET['delete_id']]);
    $message = "Job successfully deleted!";
}

// Get all jobs for listing
$jobs = $pdo->query("SELECT * FROM jobs ORDER BY posted_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get job for editing if edit_id is set
$editJob = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id=?");
    $stmt->execute([$_GET['edit_id']]);
    $editJob = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Job Listings</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        .header {
            background-color: #954695;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .back-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .back-link:hover {
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .form-container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .section-title {
            color: #954695;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: #954695;
            outline: none;
            box-shadow: 0 0 0 2px rgba(149, 70, 149, 0.2);
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: #954695;
            color: white;
        }

        .btn-primary:hover {
            background: #7d3c7d;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .job-card {
            background: white;
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .job-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .job-title {
            color: #954695;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .job-meta {
            color: #666;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .job-description {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .job-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .text-muted {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #ddd, transparent);
            margin: 30px 0;
            border: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .back-link {
                margin-top: 10px;
            }

            .job-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Manage Job Listings</h1>
        <a href="recruiter_dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 class="section-title"><?php echo $editJob ? 'Edit Job Listing' : 'Create New Job Listing'; ?></h2>
            <form method="post">
                <?php if ($editJob): ?>
                    <input type="hidden" name="id" value="<?php echo $editJob['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Job Title</label>
                    <input type="text" id="title" name="title" required
                        value="<?php echo $editJob ? htmlspecialchars($editJob['title']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" required
                        value="<?php echo $editJob ? htmlspecialchars($editJob['company']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required
                        value="<?php echo $editJob ? htmlspecialchars($editJob['location']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="description">Job Description</label>
                    <textarea id="description" name="description" required><?php
                    echo $editJob ? htmlspecialchars($editJob['description']) : '';
                    ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" name="<?php echo $editJob ? 'update_job' : 'add_job'; ?>">
                    <?php echo $editJob ? 'Update Job' : 'Post Job'; ?>
                </button>

                <?php if ($editJob): ?>
                    <a href="manage_jobs.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="divider"></div>

        <h2 class="section-title">Current Job Listings</h2>

        <?php if (empty($jobs)): ?>
            <p>No job listings found. Create your first job posting above.</p>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
                    <div class="job-meta">
                        <strong>Company:</strong> <?php echo htmlspecialchars($job['company']); ?> |
                        <strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?>
                    </div>
                    <div class="job-description">
                        <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                    </div>
                    <div class="text-muted">
                        <strong>Posted:</strong> <?php echo date('M j, Y', strtotime($job['posted_date'])); ?>
                    </div>

                    <div class="job-actions">
                        <a href="manage_jobs.php?edit_id=<?php echo $job['id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="manage_jobs.php?delete_id=<?php echo $job['id']; ?>" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this job?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>