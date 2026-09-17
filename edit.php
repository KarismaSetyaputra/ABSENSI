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

// Proses update
if (isset($_POST['update'])) {
    $tanggal = $_POST['tanggal_absensi'];
    $waktu = $_POST['waktu_absensi'];
    $status = $_POST['status'];
    $keterangan = mysqli_real_escape_string($koneksi, trim($_POST['keterangan']));

    if (!in_array($status, ['Hadir', 'Izin', 'Sakit'])) {
        header("Location: edit.php?id=$id&msg=" . urlencode("Status tidak valid"));
        exit;
    }

    $keterangan_sql = $keterangan !== '' ? "'$keterangan'" : "NULL";

    mysqli_query($koneksi, "
        UPDATE absensi SET
        tanggal_absensi='$tanggal',
        waktu_absensi='$waktu',
        status='$status',
        keterangan=$keterangan_sql
        WHERE id_absensi='$id' AND id_siswa='$id_siswa'
    ");

    header("Location: dashboard_murid.php?msg=" . urlencode("Absensi berhasil diperbarui"));
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Absensi</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=3">
    <style>
        .status-switch {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }
        .status-switch label {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid var(--glass-border, #DCE4DF);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-muted, #6D7D77);
            background: rgba(255, 255, 255, 0.06);
            transition: all 0.2s ease;
        }
        .status-switch input[type="radio"] { display: none; }
        .status-switch label.active-hadir { background: var(--success-tint); border-color: var(--accent-cyan); color: var(--accent-cyan); }
        .status-switch label.active-izin  { background: var(--warning-tint); border-color: var(--accent-gold); color: var(--accent-gold); }
        .status-switch label.active-sakit { background: var(--danger-tint); border-color: #E2564A; color: #F17A6E; }
        .foto-preview {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid var(--glass-border);
        }
        .status-switch label {
            background: var(--neutral-tint, rgba(255,255,255,0.06));
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<form action="edit.php" method="POST">
    <h2>Edit Absensi</h2>

    <input type="hidden" name="id" value="<?= $d['id_absensi']; ?>">

    <label>Status</label>
    <div class="status-switch">
        <label id="label-hadir">
            <input type="radio" name="status" value="Hadir" onchange="updateStatus()" <?= $d['status'] === 'Hadir' ? 'checked' : ''; ?>>
            <span>Hadir</span>
        </label>
        <label id="label-izin">
            <input type="radio" name="status" value="Izin" onchange="updateStatus()" <?= $d['status'] === 'Izin' ? 'checked' : ''; ?>>
            <span>Izin</span>
        </label>
        <label id="label-sakit">
            <input type="radio" name="status" value="Sakit" onchange="updateStatus()" <?= $d['status'] === 'Sakit' ? 'checked' : ''; ?>>
            <span>Sakit</span>
        </label>
    </div>

    <?php if ($d['foto']): ?>
        <label>Foto Bukti Saat Ini</label>
        <img src="<?= htmlspecialchars($d['foto']); ?>" class="foto-preview" alt="Foto absensi">
    <?php endif; ?>

    <label>Keterangan (untuk Izin/Sakit)</label>
    <textarea name="keterangan" placeholder="Tulis alasan izin/sakit"><?= htmlspecialchars($d['keterangan']); ?></textarea>

    <label>Tanggal Absensi</label>
    <input type="date" name="tanggal_absensi" value="<?= $d['tanggal_absensi']; ?>" required>

    <label>Waktu Absensi</label>
    <input type="time" name="waktu_absensi" value="<?= substr($d['waktu_absensi'], 0, 5); ?>" required>

    <button type="submit" name="update">Update</button>
    <br><br>
    <a href="dashboard_murid.php">← Batal / Kembali</a>
</form>

<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>

<script>
    function updateStatus() {
        const status = document.querySelector('input[name="status"]:checked').value;
        document.getElementById('label-hadir').classList.toggle('active-hadir', status === 'Hadir');
        document.getElementById('label-izin').classList.toggle('active-izin', status === 'Izin');
        document.getElementById('label-sakit').classList.toggle('active-sakit', status === 'Sakit');
    }
    updateStatus();
</script>

</body>
</html>
