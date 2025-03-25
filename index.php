<?php
/**
 * Admin Dashboard - Index Page
 * 
 * This is the main entry point for the admin dashboard
 */

// 1. Initialize session and check authentication
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect if not admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}

// 2. Set page variables
$page_title = "Admin Dashboard";
$current_page = "dashboard";

// 3. Include database connection (uncomment when ready)
// require_once 'includes/db_connection.php';

// 4. Include header
require_once 'includes/header.php';
?>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>

    <!-- Student Review Section -->
    <div class="student-review">
        <h2>Student Review</h2>
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('visa')">Visa Students</button>
            <button class="tab-button" onclick="showTab('non-visa')">Non-Visa Students</button>
        </div>

        <!-- Visa Students Tab -->
        <div id="visa" class="tab-content active">
            <?php include 'includes/tables/visa_students.php'; ?>
        </div>

        <!-- Non-Visa Students Tab -->
        <div id="non-visa" class="tab-content">
            <?php include 'includes/tables/non_visa_students.php'; ?>
        </div>
    </div>

    <!-- Recruitment Request Form -->
    <form action="actions/handle_request.php" method="POST" class="dashboard-form">
        <h2>Recruitment Request</h2>
        <div class="form-group">
            <label for="student_id">Student ID:</label>
            <input type="text" name="student_id" required>
        </div>
        <div class="form-group">
            <label for="requested_hours">Requested Hours:</label>
            <input type="number" name="requested_hours" required>
        </div>
        <button type="submit" name="request_hours" class="btn-primary">Submit Request</button>
    </form>

    <!-- Timesheet Submission Form -->
    <form action="actions/submit_timesheet.php" method="POST" class="dashboard-form">
        <h2>Submit Timesheet</h2>
        <div class="form-group">
            <label for="student_id">Student ID:</label>
            <input type="text" name="student_id" required>
        </div>
        <div class="form-group">
            <label for="worked_hours">Worked Hours:</label>
            <input type="number" name="worked_hours" required>
        </div>
        <button type="submit" name="submit_timesheet" class="btn-primary">Submit Timesheet</button>
    </form>
</div>

<?php
// 5. Include footer
require_once 'includes/footer.php';
?>