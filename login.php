<?php
// login.php (FINAL - SECURE MySQLi VALIDATION)
include 'db.php'; // Defines $conn and calls session_start()

$login_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Define variables
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // 2. Fetch user by username using MySQLi prepared statement
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    
    if ($stmt === false) {
        $login_error = "Database error: Could not prepare statement.";
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        // 3. Verify user and password hash
        if ($user && password_verify($password, $user['password'])) {
            
            // Success! Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: index.php");
            exit;
        } else {
            $login_error = "Invalid username or password!"; 
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 400px;">
    <h2 class="text-center mb-4">Login</h2>

    <?php if ($login_error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($login_error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required 
                   value="<?= (isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '') ?>" />
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
</body>
</html>