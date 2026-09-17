<?php
session_start();

// Jika pengguna sudah login, arahkan ke dashboard sesuai role masing-masing
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
        exit;
    } elseif ($_SESSION['role'] === 'murid') {
        header("Location: dashboard_murid.php");
        exit;
    }
}

// Jika belum login, arahkan langsung ke halaman login
header("Location: login.php");
exit;
?>