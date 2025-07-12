<?php
$dsn  = "mysql:host=localhost;dbname=blog1;charset=utf8mb4";
$user = "root";
$pass = "";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit("DB Connection failed: " . $e->getMessage());
}

session_start();
