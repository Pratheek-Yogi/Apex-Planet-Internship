<?php
include 'db.php';
include 'auth.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: index.php");
exit;
