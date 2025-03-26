<?php
$host = '127.0.0.1';
$dbname = 'campusjobs';
$username = "root";
$password = "";
// $username = 'campusjobs';
// $password = 'JjseKOHzkmrSwBzy';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to database!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>