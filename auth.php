<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

function requireRole($role) {
    if ($_SESSION['role'] !== $role) {
        header("Location: ../index.php?error=access_denied");
        exit;
    }
}
?>