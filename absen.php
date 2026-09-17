<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'murid') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$id_siswa = $_SESSION['id_siswa'];
$today = date('Y-m-d');
$now = date('H:i:s');

// Cegah mengisi absensi dua kali di hari yang sama
$cek = mysqli_query($koneksi, "SELECT * FROM absensi WHERE id_siswa='$id_siswa' AND tanggal_absensi='$today'");

if (mysqli_num_rows($cek) > 0) {
    header("Location: dashboard_murid.php?msg=" . urlencode("Kamu sudah mengisi absensi hari ini"));
    exit;
}

if (!isset($_POST['kirim_absen'])) {
    header("Location: dashboard_murid.php");
    exit;
}

$status = isset($_POST['status']) ? $_POST['status'] : '';
$keterangan_raw = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';
$foto_path = null;

if (!in_array($status, ['Hadir', 'Izin', 'Sakit'])) {
    header("Location: dashboard_murid.php?msg=" . urlencode("Status tidak valid"));
    exit;
}

if ($status === 'Hadir') {

    // Wajib upload foto sebagai bukti berada di area sekolah
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        header("Location: dashboard_murid.php?msg=" . urlencode("Foto wajib diupload untuk absen Hadir"));
        exit;
    }

    $file = $_FILES['foto'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed)) {
        header("Location: dashboard_murid.php?msg=" . urlencode("Format foto harus JPG atau PNG"));
        exit;
    }

    if ($file['size'] > 3 * 1024 * 1024) { // maksimal 3MB
        header("Location: dashboard_murid.php?msg=" . urlencode("Ukuran foto maksimal 3MB"));
        exit;
    }

    // Pastikan file yang diupload benar-benar gambar, bukan file lain yang disamarkan
    $cek_gambar = @getimagesize($file['tmp_name']);
    if ($cek_gambar === false) {
        header("Location: dashboard_murid.php?msg=" . urlencode("File yang diupload bukan gambar yang valid"));
        exit;
    }

    $upload_dir = "uploads/absensi/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $nama_file = "absen_" . $id_siswa . "_" . date('Ymd_His') . "." . $ext;
    $tujuan = $upload_dir . $nama_file;

    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        header("Location: dashboard_murid.php?msg=" . urlencode("Gagal menyimpan foto, coba lagi"));
        exit;
    }

    $foto_path = $tujuan;

} else {

    // Izin / Sakit wajib isi keterangan
    if ($keterangan_raw === '') {
        header("Location: dashboard_murid.php?msg=" . urlencode("Keterangan wajib diisi untuk Izin/Sakit"));
        exit;
    }
}

$keterangan = mysqli_real_escape_string($koneksi, $keterangan_raw);
$foto_sql = $foto_path ? "'" . mysqli_real_escape_string($koneksi, $foto_path) . "'" : "NULL";
$keterangan_sql = $keterangan !== '' ? "'$keterangan'" : "NULL";

mysqli_query($koneksi, "
    INSERT INTO absensi (id_siswa, status, tanggal_absensi, waktu_absensi, foto, keterangan)
    VALUES ('$id_siswa', '$status', '$today', '$now', $foto_sql, $keterangan_sql)
");

header("Location: dashboard_murid.php?msg=" . urlencode("Absensi berhasil dicatat"));
exit;
?>
