
<?php
require 'auth.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $_SESSION['user_id']);
    $stmt->execute();
    header("Location: dashboard.php");
}
?>
<form method="post">
    <input name="title" required placeholder="Title">
    <textarea name="content" required placeholder="Content"></textarea>
    <button type="submit">Create</button>
</form>
