<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'murid') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$id_siswa = $_SESSION['id_siswa'];
$id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null);

if (!$id) {
    header("Location: dashboard_murid.php");
    exit;
}

// Pastikan absensi ini benar-benar milik murid yang sedang login
$data = mysqli_query($koneksi, "SELECT * FROM absensi WHERE id_absensi='$id' AND id_siswa='$id_siswa'");
$d = mysqli_fetch_array($data);

if (!$d) {
    header("Location: dashboard_murid.php?msg=" . urlencode("Data absensi tidak ditemukan"));
    exit;
}

// Jika tombol "Ya, Hapus" diklik
if (isset($_POST['confirm_delete'])) {
    // Hapus juga file foto dari server kalau ada, biar tidak jadi file sampah
    if (!empty($d['foto']) && file_exists($d['foto'])) {
        unlink($d['foto']);
    }
    mysqli_query($koneksi, "DELETE FROM absensi WHERE id_absensi='$id' AND id_siswa='$id_siswa'");
    header("Location: dashboard_murid.php?msg=" . urlencode("Absensi berhasil dihapus"));
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Hapus Absensi</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=3">
    <style>
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .modal-box {
            max-width: 420px;
            width: 90%;
            padding: 30px;
            text-align: center;
            background: var(--glass-bg, #FFFFFF);
            border: 1px solid var(--glass-border, #DCE4DF);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(22, 36, 30, 0.1);
            position: relative;
            z-index: 11;
        }
        .modal-icon { font-size: 45px; margin-bottom: 15px; display: inline-block; }
        .modal-box h3 {
            font-family: 'Poppins', sans-serif;
            color: var(--text-heading, #16241E);
            font-size: 1.3rem;
            margin-bottom: 10px;
        }
        .modal-box p {
            font-size: 0.95rem;
            color: var(--text-color, #33413C);
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .absen-info {
            background: rgba(0, 0, 0, 0.2);
            padding: 10px;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--accent-gold);
        }
        .button-group { display: flex; gap: 12px; justify-content: center; }
        .btn-delete {
            background: var(--danger-bg, #C1443B);
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
        }
        .btn-delete:hover {
            background: var(--danger-hover, #A3372F);
        }
        .btn-cancel {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-color);
            border: 1px solid var(--glass-border);
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-block;
            text-align: center;
            width: 100%;
        }
        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.12);
            color: var(--text-heading);
            border-color: var(--accent-gold);
        }
    </style>
</head>
<body>

<div class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <h3>Konfirmasi Hapus</h3>
        <p>Apakah kamu yakin ingin menghapus data absensi ini? Data yang sudah dihapus tidak bisa dikembalikan.</p>

        <div class="absen-info">
            <?= date('d-m-Y', strtotime($d['tanggal_absensi'])); ?> - <?= substr($d['waktu_absensi'], 0, 5); ?>
        </div>

        <form method="POST">
            <input type="hidden" name="id" value="<?= $d['id_absensi']; ?>">
            <div class="button-group">
                <button type="submit" name="confirm_delete" class="btn-delete">Ya, Hapus</button>
                <a href="dashboard_murid.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>

</body>
</html>
