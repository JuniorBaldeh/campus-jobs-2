<!-- Admin Dashboard -->
<?php
// admin_dashboard.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>