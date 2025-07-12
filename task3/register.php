<?php
require 'db.php';

$register_error = "";
$register_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = 'viewer'; // Default role

    if (strlen($username) < 3 || strlen($password) < 5) {
        $register_error = "Username or password too short.";
    } else {
        // Check if user already exists
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);

        if ($check->fetch()) {
            $register_error = "Username already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed, $role]);
            $register_success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Secure Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 460px;">
    <h3 class="text-center mb-4">📝 Register New Account</h3>

    <?php if ($register_error): ?>
        <div class="alert alert-danger"><?= $register_error ?></div>
    <?php elseif ($register_success): ?>
        <div class="alert alert-success">
            Account created! <a href="login.php">Login here</a>.
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label>Username</label>
            <input name="username" class="form-control" required minlength="3">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input name="password" type="password" class="form-control" required minlength="5">
        </div>
        <button class="btn btn-success w-100">Register</button>
    </form>
</div>
</body>
</html>
