<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$kembali = ($_SESSION['role'] === 'admin') ? 'dashboard_admin.php' : 'dashboard_murid.php';

// Jika tombol "Ya, Logout" diklik
if (isset($_POST['confirm_logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Logout</title>
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
        .button-group { display: flex; gap: 12px; justify-content: center; }
        .btn-logout {
            background: var(--button-bg, #2F6F5E);
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
        }
        .btn-logout:hover {
            background: var(--button-hover, #234F42);
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
        <div class="modal-icon">🚪</div>
        <h3>Konfirmasi Logout</h3>
        <p>Apakah kamu yakin ingin keluar dari sistem?</p>

        <form method="POST">
            <div class="button-group">
                <button type="submit" name="confirm_logout" class="btn-logout">Ya, Logout</button>
                <a href="<?= $kembali; ?>" class="btn-cancel">Batal</a>
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
