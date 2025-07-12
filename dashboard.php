
<?php
require 'auth.php';
$result = $conn->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC");
echo "<a href='create.php'>New Post</a> | <a href='logout.php'>Logout</a><hr>";
while ($row = $result->fetch_assoc()) {
    echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
    echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
    echo "<small>Posted by " . $row['username'] . " at " . $row['created_at'] . "</small><br>";
    echo "<a href='edit.php?id={$row['id']}'>Edit</a> | <a href='delete.php?id={$row['id']}'>Delete</a><hr>";
}
?>
