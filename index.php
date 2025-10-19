<?php
// index.php (FINAL - MySQLi)
include 'db.php'; // Defines $conn
include 'auth.php'; // Checks login status

$success = false;
$user_id = $_SESSION['user_id'] ?? null; 

// --- POST Creation Logic ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        // MySQLi Prepared Statement for INSERT
        $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $content, $user_id);
        $stmt->execute();
        $stmt->close();
        $success = true;
    }
}

// --- Pagination and Search Logic ---
$search = $_GET['search'] ?? "";
$page = $_GET['page'] ?? 1;
$limit = 5;
$start = ($page - 1) * $limit;

$params = [];
$types = '';
$where = '';

if ($search) {
    $where = "WHERE title LIKE ? OR content LIKE ?";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types = 'ss';
}

// SQL Query for total count
$count_sql = "SELECT COUNT(*) as total FROM posts $where";
$count_stmt = $conn->prepare($count_sql);
if ($search) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();
$total_pages = ceil($total / $limit);

// SQL Query for posts
$sql = "SELECT * FROM posts $where ORDER BY id DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);

// Add LIMIT parameters (integer types 'ii') to the params array
$params[] = $start;
$params[] = $limit;
$types .= 'ii';

// Bind all parameters together
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">📘 My Blog</h1>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>

    <?php if ($success): ?>
        <div id="alert" class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ Post added successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="mb-0">Manage Blog</h4>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#addPostForm">➕ Add New Post</button>
    </div>

    <div class="collapse" id="addPostForm">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">Add New Post</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3"><input type="text" name="title" class="form-control" placeholder="Title" required></div>
                    <div class="mb-3"><textarea name="content" class="form-control" rows="4" placeholder="Content" required></textarea></div>
                    <button class="btn btn-success" type="submit">Post</button>
                </form>
            </div>
        </div>
    </div>

    <form method="GET" class="row justify-content-center mb-4">
        <div class="col-md-6"><input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?= htmlspecialchars($search); ?>"></div>
        <div class="col-auto"><button class="btn btn-outline-primary">Search</button></div>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5><?= htmlspecialchars($row['title']) ?></h5>
                    <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this post?')">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center text-muted">No posts found.</p>
    <?php endif; ?>
    <?php $stmt->close(); ?>

    <nav>
        <ul class="pagination justify-content-center mt-4">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php $link = "?page=$i" . ($search ? "&search=" . urlencode($search) : ""); ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>"><a class="page-link" href="<?= $link ?>"><?= $i ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(() => {
        const alert = document.getElementById('alert');
        if (alert) {
            alert.classList.remove('show');
        }
    }, 5000);
</script>
</body>
</html>