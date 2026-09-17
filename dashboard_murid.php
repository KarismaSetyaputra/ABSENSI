<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'murid') {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$id_siswa = $_SESSION['id_siswa'];
$today = date('Y-m-d');
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// Cek apakah sudah mengisi absensi hari ini
$cek = mysqli_query($koneksi, "SELECT * FROM absensi WHERE id_siswa='$id_siswa' AND tanggal_absensi='$today'");
$sudah_absen = mysqli_fetch_array($cek);

// Riwayat absensi murid ini
$riwayat = mysqli_query($koneksi, "
    SELECT * FROM absensi
    WHERE id_siswa='$id_siswa'
    ORDER BY tanggal_absensi DESC, waktu_absensi DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Murid - Absensi</title>
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
        .absen-box {
            background: var(--glass-bg, #FFFFFF);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .absen-status {
            font-size: 1.05rem;
            margin-bottom: 15px;
            color: var(--text-color);
            text-align: center;
        }
        .btn-absen {
            background: var(--button-bg);
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            width: auto;
        }
        .btn-absen:disabled {
            background: #DCE4DF;
            color: var(--text-muted, #6D7D77);
            cursor: not-allowed;
        }
        .alert-msg {
            background: var(--warning-tint);
            border: 1px solid var(--warning-border);
            color: var(--accent-gold);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .action-links a {
            font-size: 0.9rem;
            margin: 0 4px;
        }

        /* Status pilihan (Hadir / Izin / Sakit) */
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
            background: var(--glass-bg, #FFFFFF);
            transition: all 0.2s ease;
        }
        .status-switch input[type="radio"] { display: none; }
        .status-switch label.active-hadir {
            background: var(--success-tint);
            border-color: var(--accent-cyan);
            color: var(--accent-cyan);
        }
        .status-switch label.active-izin {
            background: var(--warning-tint);
            border-color: var(--accent-gold);
            color: var(--accent-gold);
        }
        .status-switch label.active-sakit {
            background: var(--danger-tint);
            border-color: #E2564A;
            color: #F17A6E;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 6px;
            background: var(--glass-bg, #FFFFFF);
            color: var(--text-color);
            border: 1px dashed var(--glass-border);
            border-radius: 8px;
        }
        .field-hint {
            font-size: 0.8rem;
            color: var(--text-muted, #6D7D77);
            margin-bottom: 15px;
        }

        /* Badge status di tabel riwayat */
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
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h2>Halo, <?= htmlspecialchars($_SESSION['nama']); ?></h2>
        <a href="logout.php" class="btn-logout-top">Logout</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert-msg"><?= htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="absen-box">
        <?php if ($sudah_absen): ?>
            <?php
                $statusLabel = $sudah_absen['status'];
                $badgeClass = $statusLabel === 'Hadir' ? 'badge-hadir' : ($statusLabel === 'Izin' ? 'badge-izin' : 'badge-sakit');
            ?>
            <p class="absen-status">
                ✅ Kamu sudah mengisi absensi hari ini:
                <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($statusLabel); ?></span>
                jam <strong><?= substr($sudah_absen['waktu_absensi'], 0, 5); ?></strong>
            </p>
            <div style="text-align:center;">
                <button class="btn-absen" disabled>Sudah Diisi Hari Ini</button>
            </div>
        <?php else: ?>
            <p class="absen-status">Kamu belum mengisi absensi hari ini (<?= date('d-m-Y'); ?>)</p>

            <form action="absen.php" method="POST" enctype="multipart/form-data">
                <div class="status-switch">
                    <label id="label-hadir">
                        <input type="radio" name="status" value="Hadir" onchange="updateStatus()" checked>
                        <span>Hadir</span>
                    </label>
                    <label id="label-izin">
                        <input type="radio" name="status" value="Izin" onchange="updateStatus()">
                        <span>Izin</span>
                    </label>
                    <label id="label-sakit">
                        <input type="radio" name="status" value="Sakit" onchange="updateStatus()">
                        <span>Sakit</span>
                    </label>
                </div>

                <div id="field-foto">
                    <label>Foto Bukti (harus di area sekolah)</label>
                    <input type="file" name="foto" accept="image/*" capture="environment">
                    <p class="field-hint">Wajib diupload sebagai bukti kamu absen langsung di sekolah. Format JPG/PNG, maks 3MB.</p>
                </div>

                <div id="field-keterangan" style="display:none;">
                    <label>Keterangan</label>
                    <textarea name="keterangan" placeholder="Tulis alasan izin/sakit kamu"></textarea>
                </div>

                <button type="submit" name="kirim_absen" class="btn-absen" style="width:100%;">Kirim Absensi</button>
            </form>
        <?php endif; ?>
    </div>

    <h3 style="margin-bottom:10px; color:var(--text-heading);">Riwayat Absensi Kamu</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Status</th>
            <th>Foto</th>
            <th>Aksi</th>
        </tr>
        <?php $no = 1; while ($r = mysqli_fetch_array($riwayat)):
            $badgeClass = $r['status'] === 'Hadir' ? 'badge-hadir' : ($r['status'] === 'Izin' ? 'badge-izin' : 'badge-sakit');
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= date('d-m-Y', strtotime($r['tanggal_absensi'])); ?></td>
            <td><?= substr($r['waktu_absensi'], 0, 5); ?></td>
            <td><span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($r['status']); ?></span></td>
            <td>
                <?php if ($r['foto']): ?>
                    <a href="<?= htmlspecialchars($r['foto']); ?>" target="_blank">Lihat</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td class="action-links">
                <a href="edit.php?id=<?= $r['id_absensi']; ?>">Edit</a> |
                <a href="hapus.php?id=<?= $r['id_absensi']; ?>">Hapus</a>
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

<script>
    function updateStatus() {
        const status = document.querySelector('input[name="status"]:checked').value;

        document.getElementById('label-hadir').classList.toggle('active-hadir', status === 'Hadir');
        document.getElementById('label-izin').classList.toggle('active-izin', status === 'Izin');
        document.getElementById('label-sakit').classList.toggle('active-sakit', status === 'Sakit');

        const isHadir = status === 'Hadir';
        document.getElementById('field-foto').style.display = isHadir ? 'block' : 'none';
        document.getElementById('field-keterangan').style.display = isHadir ? 'none' : 'block';

        document.querySelector('input[name="foto"]').required = isHadir;
        document.querySelector('textarea[name="keterangan"]').required = !isHadir;
    }
    updateStatus();
</script>

</body>
</html>
