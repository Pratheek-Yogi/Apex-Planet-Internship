<?php
require 'db.php'; require 'auth.php'; gate(['admin']);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php"); exit;
