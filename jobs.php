<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit();
}

// Display error/success messages
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Job</title>
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
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            color: #954695;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .btn {
            padding: 8px 15px;
            background-color: #954695;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        .btn:hover {
            background-color: #7d3c7d;
        }

        .btn-cancel {
            background-color: #d9534f;
        }

        .btn-cancel:hover {
            background-color: #c9302c;
        }

        .error-message {
            color: #d9534f;
            background: #f8d7da;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        .success-message {
            color: #155724;
            background: #d4edda;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Post New Job</h1>
        <a href="recruiter_dashboard.php" class="btn btn-cancel">Back to Dashboard</a>
    </div>

    <div class="container">
        <h2 class="form-title">Job Details</h2>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="process_job.php">
            <div class="form-group">
                <label for="title">Job Title*</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="location">Location*</label>
                    <input type="text" id="location" name="location" required>
                </div>

                <div class="form-group">
                    <label for="salary">Salary Range</label>
                    <input type="text" id="salary" name="salary">
                </div>
            </div>

            <div class="form-group">
                <label for="job_type">Job Type*</label>
                <select id="job_type" name="job_type" required>
                    <option value="">Select job type</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Contract">Contract</option>
                    <option value="Internship">Internship</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Job Description*</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="requirements">Requirements*</label>
                <textarea id="requirements" name="requirements" required></textarea>
            </div>

            <!-- In your form tag, change the action to process_jobs.php -->
            <form method="POST" action="process_jobs.php">

                <!-- The submit button will now go to process_jobs.php when clicked -->
                <div class="form-group">
                    <button type="submit" class="btn">Post Job</button>
                    <button type="reset" class="btn btn-cancel">Reset Form</button>
                </div>
            </form>
    </div>
</body>

</html>