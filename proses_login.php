<?php

session_start();
include "koneksi.php";

if (isset($_POST['login'])) {

    $role = $_POST['role'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    if ($role === 'admin') {

        // ================= LOGIN ADMIN =================
        $query = mysqli_query($koneksi, "
            SELECT * FROM admin
            WHERE username='$username'
            AND password='$password'
        ");

        if (mysqli_num_rows($query) > 0) {
            $_SESSION['login'] = true;
            $_SESSION['role'] = 'admin';
            $_SESSION['username'] = $username;
            header("Location: dashboard_admin.php");
            exit;
        } else {
            header("Location: login.php?role=admin&error=" . urlencode("Username atau Password Admin salah"));
            exit;
        }

    } elseif ($role === 'murid') {

        // ================= LOGIN MURID (pakai NIS) =================
        $query = mysqli_query($koneksi, "
            SELECT * FROM siswa
            WHERE nis='$username'
            AND password='$password'
        ");

        $data = mysqli_fetch_array($query);

        if ($data) {
            $_SESSION['login'] = true;
            $_SESSION['role'] = 'murid';
            $_SESSION['id_siswa'] = $data['id_siswa'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['nis'] = $data['nis'];
            header("Location: dashboard_murid.php");
            exit;
        } else {
            header("Location: login.php?role=murid&error=" . urlencode("NIS atau Password salah"));
            exit;
        }

    } else {
        header("Location: login.php?error=" . urlencode("Role tidak valid"));
        exit;
    }

}

?>
