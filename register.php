<?php
// register.php (FINAL - MySQLi)

require 'db.php'; // Defines $conn

$register_error = "";
$register_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'viewer'; // Default role

    if (strlen($username) < 3 || strlen($password) < 5) {
        $register_error = "Username must be at least 3 characters and password must be at least 5 characters.";
    } else {
        // --- Check if user already exists (MySQLi Prepared Statement) ---
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        
        if (!$check) {
            $register_error = "Database preparation error (Check): " . $conn->error;
        } else {
            $check->bind_param("s", $username);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $register_error = "Username already taken.";
            } else {
                // --- Insert New User ---
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                
                if (!$stmt) {
                    $register_error = "Database preparation error (Insert): " . $conn->error;
                } else {
                    $stmt->bind_param("sss", $username, $hashed, $role);
                    $stmt->execute();
                    $stmt->close();
                    $register_success = true;
                }
            }
            $check->close();
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
        <div class="alert alert-danger"><?= htmlspecialchars($register_error) ?></div>
    <?php elseif ($register_success): ?>
        <div class="alert alert-success">
            Account created! <a href="login.php">Login here</a>.
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input 
                name="username" 
                class="form-control" 
                required minlength="3"
                value="<?= (isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '') ?>"
            >
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required minlength="5">
        </div>
        <button class="btn btn-success w-100">Register</button>
    </form>
</div>
</body>
</html>