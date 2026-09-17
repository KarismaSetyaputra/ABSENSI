<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$today = date('Y-m-d');

// Ringkasan
$total_siswa_q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM siswa");
$total_siswa = mysqli_fetch_assoc($total_siswa_q)['total'];

$hadir_q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM absensi WHERE tanggal_absensi = '$today' AND status = 'Hadir'");
$total_hadir = mysqli_fetch_assoc($hadir_q)['total'];

$izin_q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM absensi WHERE tanggal_absensi = '$today' AND status = 'Izin'");
$total_izin = mysqli_fetch_assoc($izin_q)['total'];

$sakit_q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM absensi WHERE tanggal_absensi = '$today' AND status = 'Sakit'");
$total_sakit = mysqli_fetch_assoc($sakit_q)['total'];

$sudah_isi = $total_hadir + $total_izin + $total_sakit;
$belum_isi = $total_siswa - $sudah_isi;

// Status kehadiran semua siswa hari ini
$status_hari_ini = mysqli_query($koneksi, "
    SELECT s.id_siswa, s.nis, s.nama, s.jurusan, a.status, a.waktu_absensi, a.foto, a.keterangan
    FROM siswa s
    LEFT JOIN absensi a ON s.id_siswa = a.id_siswa AND a.tanggal_absensi = '$today'
    ORDER BY s.nama ASC
");

// Riwayat absensi lengkap (50 terbaru)
$riwayat = mysqli_query($koneksi, "
    SELECT a.id_absensi, s.nis, s.nama, a.status, a.tanggal_absensi, a.waktu_absensi, a.foto, a.keterangan
    FROM absensi a
    JOIN siswa s ON a.id_siswa = s.id_siswa
    ORDER BY a.tanggal_absensi DESC, a.waktu_absensi DESC
    LIMIT 50
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - Absensi</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=3">
    <style>
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-logout-top {
            background: var(--danger-bg, #C1443B);
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            width: auto;
            display: inline-block;
        }
        .summary-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .card {
            flex: 1;
            min-width: 130px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
        }
        .card .num {
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--text-heading);
        }
        .card .label {
            font-size: 0.82rem;
            color: var(--text-muted, #6D7D77);
            margin-top: 4px;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-hadir { background: var(--success-tint); color: var(--accent-cyan); border: 1px solid var(--success-border); }
        .badge-izin  { background: var(--warning-tint); color: var(--accent-gold); border: 1px solid var(--warning-border); }
        .badge-sakit { background: var(--danger-tint); color: #F17A6E; border: 1px solid var(--danger-border); }
        .badge-belum {
            background: var(--neutral-tint);
            color: var(--text-muted);
            border: 1px solid var(--neutral-border);
        }
        h3.section-title {
            margin: 30px 0 10px 0;
            color: var(--text-heading);
        }
        .keterangan-cell {
            font-size: 0.85rem;
            color: var(--text-muted, #6D7D77);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h2>Dashboard Admin - Absensi</h2>
        <a href="logout.php" class="btn-logout-top">Logout</a>
    </div>

    <div class="summary-cards">
        <div class="card">
            <div class="num"><?= $total_siswa; ?></div>
            <div class="label">Total Murid</div>
        </div>
        <div class="card">
            <div class="num"><?= $total_hadir; ?></div>
            <div class="label">Hadir Hari Ini</div>
        </div>
        <div class="card">
            <div class="num"><?= $total_izin; ?></div>
            <div class="label">Izin Hari Ini</div>
        </div>
        <div class="card">
            <div class="num"><?= $total_sakit; ?></div>
            <div class="label">Sakit Hari Ini</div>
        </div>
        <div class="card">
            <div class="num"><?= $belum_isi; ?></div>
            <div class="label">Belum Mengisi</div>
        </div>
    </div>

    <h3 class="section-title">Status Kehadiran Hari Ini (<?= date('d-m-Y'); ?>)</h3>
    <table>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Jurusan</th>
            <th>Status</th>
            <th>Keterangan / Foto</th>
        </tr>
        <?php $no = 1; while ($s = mysqli_fetch_array($status_hari_ini)):
            $badgeClass = $s['status'] === 'Hadir' ? 'badge-hadir' : ($s['status'] === 'Izin' ? 'badge-izin' : ($s['status'] === 'Sakit' ? 'badge-sakit' : 'badge-belum'));
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($s['nis']); ?></td>
            <td><?= htmlspecialchars($s['nama']); ?></td>
            <td><?= htmlspecialchars($s['jurusan']); ?></td>
            <td>
                <?php if ($s['status']): ?>
                    <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($s['status']); ?> - <?= substr($s['waktu_absensi'], 0, 5); ?></span>
                <?php else: ?>
                    <span class="badge badge-belum">Belum Mengisi</span>
                <?php endif; ?>
            </td>
            <td class="keterangan-cell">
                <?php if ($s['foto']): ?>
                    <a href="<?= htmlspecialchars($s['foto']); ?>" target="_blank">Lihat Foto</a>
                <?php elseif ($s['keterangan']): ?>
                    <?= htmlspecialchars($s['keterangan']); ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3 class="section-title">Riwayat Absensi Terbaru</h3>
    <table>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Keterangan / Foto</th>
        </tr>
        <?php $no = 1; while ($r = mysqli_fetch_array($riwayat)):
            $badgeClass = $r['status'] === 'Hadir' ? 'badge-hadir' : ($r['status'] === 'Izin' ? 'badge-izin' : 'badge-sakit');
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($r['nis']); ?></td>
            <td><?= htmlspecialchars($r['nama']); ?></td>
            <td><span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($r['status']); ?></span></td>
            <td><?= date('d-m-Y', strtotime($r['tanggal_absensi'])); ?></td>
            <td><?= substr($r['waktu_absensi'], 0, 5); ?></td>
            <td class="keterangan-cell">
                <?php if ($r['foto']): ?>
                    <a href="<?= htmlspecialchars($r['foto']); ?>" target="_blank">Lihat Foto</a>
                <?php elseif ($r['keterangan']): ?>
                    <?= htmlspecialchars($r['keterangan']); ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>

</body>
</html>
