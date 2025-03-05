<<<<<<< HEAD
<!-- Student Dashboard -->
<?php
// student_dashboard.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}
=======
<!-- Student Dashboard -->
<?php
// student_dashboard.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}
>>>>>>> 3c92ff57254c43242a42ed7e4804d86031c74c58
?>