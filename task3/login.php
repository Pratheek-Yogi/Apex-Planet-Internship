<?php
require 'db.php';

$login_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $login_error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Secure Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 420px;">
    <h3 class="text-center mb-4">🔐 Secure Login</h3>

    <?php if ($login_error): ?>
        <div class="alert alert-danger"><?= $login_error ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input name="username" class="form-control" required minlength="3">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required minlength="5">
        </div>
        <button class="btn btn-primary w-100">Login</button>
    </form>

    <p class="text-center mt-3">
        New here? <a href="register.php" class="btn btn-sm btn-outline-secondary">Register</a>
    </p>
</div>
</body>
</html>
