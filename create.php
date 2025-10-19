<?php
// create.php (Using MySQLi)
require 'db.php'; 
require 'auth.php';

$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_id) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // MySQLi Prepared Statement
    $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $user_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: index.php"); 
    exit;
}
?>
<form method="post">
    <input name="title" required placeholder="Title">
    <textarea name="content" required placeholder="Content"></textarea>
    <button type="submit">Create</button>
</form>