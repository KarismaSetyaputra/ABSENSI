<?php
session_start();
include "koneksi.php";

$error = '';

if (isset($_POST['daftar'])) {

    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    if ($password !== $konfirmasi) {

        $error = "Password dan konfirmasi password tidak sama";

    } else {

        // Pastikan NIS belum pernah dipakai
        $cek = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$nis'");

        if (mysqli_num_rows($cek) > 0) {

            $error = "NIS ini sudah terdaftar. Silakan login, atau hubungi admin kalau ini bukan kamu.";

        } else {

            $password_hash = md5($password);

            mysqli_query($koneksi, "
                INSERT INTO siswa (nis, password, nama, jurusan, alamat)
                VALUES (
                    '$nis',
                    '$password_hash',
                    '$nama',
                    '$jurusan',
                    '$alamat'
                )
            ");

            header("Location: login.php?role=murid&success=" . urlencode("Akun berhasil dibuat! Silakan login."));
            exit;

        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun Murid</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=3">
    <style>
        .error-msg {
            color: var(--danger-bg, #C1443B);
            margin-bottom: 15px;
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<form action="daftar.php" method="POST">
    <h2>Daftar Akun Murid</h2>

    <?php if ($error): ?>
        <p class="error-msg"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <label>NIS</label>
    <input type="text" name="nis" placeholder="Masukkan NIS" required>

    <label>Nama</label>
    <input type="text" name="nama" placeholder="Masukkan Nama Lengkap" required>

    <label>Jurusan</label>
    <select name="jurusan" required>
        <option value="" disabled selected>-- Pilih Jurusan --</option>
        <option value="RPL">RPL</option>
        <option value="TKJ 1">TKJ 1</option>
        <option value="TKJ 2">TKJ 2</option>
        <option value="BR 1">BR 1</option>
        <option value="BR 2">BR 2</option>
        <option value="AK 1">AK 1</option>
        <option value="AK 2">AK 2</option>
        <option value="MP 1">MP 1</option>
        <option value="MP 2">MP 2</option>
    </select>

    <label>Alamat</label>
    <textarea name="alamat" placeholder="Masukkan Alamat" required></textarea>

    <label>Password</label>
    <input type="password" name="password" placeholder="Buat password" required>

    <label>Konfirmasi Password</label>
    <input type="password" name="konfirmasi_password" placeholder="Ulangi password" required>

    <button type="submit" name="daftar">Buat Akun</button>
    <br><br>
    <a href="login.php?role=murid">← Sudah punya akun? Login</a>
</form>

<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>

</body>
</html>
