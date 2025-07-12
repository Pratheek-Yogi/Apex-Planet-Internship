<?php
require 'db.php';
require 'auth.php';

/* ——— ADD NEW POST (admin/editor) ——— */
$addSuccess = false; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPost'])) {
    gate(['admin','editor']);
    $title   = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (mb_strlen($title) < 3)      $errors[] = "Title ≥ 3 chars";
    if (mb_strlen($content) < 10)   $errors[] = "Content ≥ 10 chars";

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, author) VALUES (?,?,?)");
        $stmt->execute([$title, $content, $_SESSION['user']]);
        $addSuccess = true;
    }
}

/* ——— SEARCH + PAGINATION ——— */
$search = $_GET['search'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 5;
$offset = ($page-1)*$limit;

$params = [];
$sqlWhere = '';
if ($search) {
    $sqlWhere = "WHERE title LIKE ? OR content LIKE ?";
    $like = "%$search%";
    $params = [$like, $like];
}

/* total rows */
$total = $pdo->prepare("SELECT COUNT(*) FROM posts $sqlWhere");
$total->execute($params);
$totalRows = $total->fetchColumn();
$pages = ceil($totalRows/$limit);

/* fetch page rows */
$stmt = $pdo->prepare("SELECT * FROM posts $sqlWhere ORDER BY id DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html><html><head>
<title>Blog Secure</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container my-5">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">📘 Secure Blog</h1>
    <div>
        <span class="me-3 badge bg-success"><?= htmlspecialchars($_SESSION['role']) ?></span>
        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
    </div>
</div>

<!-- Alerts -->
<?php if($addSuccess): ?>
<div class="alert alert-success alert-dismissible fade show" id="alert" role="alert">
    Post added successfully!
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php foreach($errors as $e): ?>
<div class="alert alert-danger alert-dismissible fade show" id="alert" role="alert">
    <?= htmlspecialchars($e) ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endforeach; ?>

<!-- Add Post (admin/editor) -->
<?php if(in_array($_SESSION['role'],['admin','editor'])): ?>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h4>Manage Posts</h4>
    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#addForm">➕ Add Post</button>
</div>
<div class="collapse" id="addForm">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">New Post</div>
        <div class="card-body">
            <form method="POST" novalidate>
                <input type="hidden" name="addPost">
                <div class="mb-3">
                    <input type="text" name="title" class="form-control" placeholder="Title" required minlength="3">
                </div>
                <div class="mb-3">
                    <textarea name="content" rows="4" class="form-control" placeholder="Content" required minlength="10"></textarea>
                </div>
                <button class="btn btn-success">Publish</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Search -->
<form method="GET" class="row g-2 mb-4">
    <div class="col-sm-8 col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-primary">Search</button>
    </div>
</form>

<!-- Posts List -->
<?php foreach($posts as $p): ?>
<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($p['title']) ?></h5>
        <p class="card-text"><?= nl2br(htmlspecialchars($p['content'])) ?></p>
        <small class="text-muted">By <?= htmlspecialchars($p['author']) ?></small><br>
        <?php if($_SESSION['role']==='admin' || ($_SESSION['role']==='editor' && $_SESSION['user']===$p['author'])): ?>
        <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm mt-2">Edit</a>
        <?php endif; ?>
        <?php if($_SESSION['role']==='admin'): ?>
        <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm mt-2"
            onclick="return confirm('Delete this post?')">Delete</a>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<!-- Pagination -->
<nav>
<ul class="pagination justify-content-center">
<?php for($i=1;$i<=$pages;$i++):
    $q = "?page=$i".($search?"&search=".urlencode($search):""); ?>
<li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="<?= $q ?>"><?= $i ?></a></li>
<?php endfor; ?>
</ul>
</nav>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
setTimeout(()=>{ document.querySelectorAll('#alert').forEach(a=>a.classList.remove('show')); },5000);
</script>
</body></html>
