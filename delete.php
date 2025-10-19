<?php
// delete.php (FINAL - MySQLi)
include 'db.php';
include 'auth.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // MySQLi Prepared Statement
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php");
exit;
?>