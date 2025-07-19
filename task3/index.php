<?php
include 'config.php'; // connects using mysqli

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$search_condition = "";
if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $search_condition = "WHERE title LIKE '%$safe_search%' OR content LIKE '%$safe_search%'";
}

// Total posts count
$count_sql = "SELECT COUNT(*) AS total FROM posts $search_condition";
$count_result = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$total_posts = $count_row['total'];
$total_pages = ceil($total_posts / $limit);

// Fetch posts
$sql = "SELECT * FROM posts $search_condition ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Posts</title>
</head>
<body>
    <h2>Blog Posts</h2>

    <form method="GET">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($post = mysqli_fetch_assoc($result)): ?>
            <div style="border:1px solid #ccc; margin:10px 0; padding:10px;">
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 150))) ?>...</p>
                <small>Posted on <?= $post['created_at'] ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No posts found.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        <?php if ($page > 1): ?>
            <a href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">← Previous</a>
        <?php endif; ?>

        <strong> Page <?= $page ?> of <?= $total_pages ?> </strong>

        <?php if ($page < $total_pages): ?>
            <a href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next →</a>
        <?php endif; ?>
    </div>
</body>
</html>
