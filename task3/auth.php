<?php
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

function gate(array $allowed_roles) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: index.php");
        exit;
    }
}
