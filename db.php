<?php
// db.php (MySQLi Connection)

$host = 'localhost';
$db = 'blog';
$user = 'root';
$pass = ''; // CHANGE THIS if your MySQL has a password

// Create connection using MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();
?>