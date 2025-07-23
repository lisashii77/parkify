<?php
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user'])) {
    header('Location: auth/login.php');
    exit;
}

$role = $_SESSION['user']['role'];

if ($role === 'admin') {
    header('Location: admin/index.php');
    exit;
} elseif ($role === 'user') {
    header('Location: user/index.php');
    exit;
} else {
    echo "Peran tidak dikenali.";
}
?>
