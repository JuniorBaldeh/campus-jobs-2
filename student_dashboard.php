<!-- Student Dashboard -->
<?php
// student_dashboard.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}
<!-- Student Dashboard -->
<?php
// student_dashboard.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}
?>