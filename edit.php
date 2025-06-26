
<?php
require 'auth.php';
$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssii", $title, $content, $id, $_SESSION['user_id']);
    $stmt->execute();
    header("Location: dashboard.php");
} else {
    $stmt = $conn->prepare("SELECT title, content FROM posts WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($title, $content);
    $stmt->fetch();
}
?>
<form method="post">
    <input name="title" value="<?= htmlspecialchars($title) ?>" required>
    <textarea name="content" required><?= htmlspecialchars($content) ?></textarea>
    <button type="submit">Update</button>
</form>
