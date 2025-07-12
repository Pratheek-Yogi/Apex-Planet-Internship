<?php
require 'db.php'; require 'auth.php'; gate(['admin','editor']);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();
if(!$post) { header("Location: index.php"); exit; }

/* only admin OR author‑editor can edit */
if($_SESSION['role']!=='admin' && $_SESSION['user']!==$post['author']){
    header("Location: index.php"); exit;
}

$updated = false; $err = "";
if($_SERVER['REQUEST_METHOD']==='POST'){
    $title = trim($_POST['title']); $content = trim($_POST['content']);
    if(mb_strlen($title)<3||mb_strlen($content)<10){ $err="Invalid length"; }
    else{
        $up = $pdo->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
        $up->execute([$title,$content,$id]); $updated=true;
        header("Location: index.php"); exit;
    }
}
?>
<!DOCTYPE html><html><head>
<title>Edit</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light"><div class="container py-5" style="max-width:700px">
<h3>Edit Post</h3>
<?php if($err): ?><div class="alert alert-danger"><?= $err ?></div><?php endif; ?>
<form method="POST" novalidate>
    <div class="mb-3"><input name="title" class="form-control" required minlength="3" value="<?= htmlspecialchars($post['title']) ?>"></div>
    <div class="mb-3"><textarea name="content" rows="5" class="form-control" required minlength="10"><?= htmlspecialchars($post['content']) ?></textarea></div>
    <button class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
</div></body></html>
