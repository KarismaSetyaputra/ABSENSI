<?php
session_start();
$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
$role_selected = isset($_GET['role']) ? $_GET['role'] : 'admin';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=3">
    <style>
        .role-switch {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .role-switch label {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid var(--glass-border, #DCE4DF);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-muted, #6D7D77);
            transition: all 0.2s ease;
        }
        .role-switch input[type="radio"] {
            display: none;
        }
        .role-switch input[type="radio"]:checked + span {
            color: #ffffff;
        }
        .role-switch label {
            background: var(--neutral-tint, rgba(255,255,255,0.06));
        }
        .role-switch label.active {
            background: var(--warning-tint);
            border-color: var(--accent-gold);
            color: var(--accent-gold);
        }
        .error-msg {
            color: var(--danger-bg, #C1443B);
            margin-bottom: 15px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .success-msg {
            color: var(--accent-cyan);
            background: var(--success-tint);
            border: 1px solid var(--success-border);
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .hint {
            font-size: 0.8rem;
            color: var(--text-muted, #6D7D77);
            margin-top: -10px;
            margin-bottom: 15px;
        }
        .register-link {
            text-align: center;
            margin-top: 10px;
            font-size: 0.9rem;
            color: var(--text-muted, #6D7D77);
        }
    </style>
</head>
<body>

<form action="proses_login.php" method="POST" id="login-form">
    <h2>Login</h2>

    <?php if ($error): ?>
        <p class="error-msg"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success-msg"><?= htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <div class="role-switch">
        <label id="label-admin" class="<?= $role_selected === 'admin' ? 'active' : ''; ?>">
            <input type="radio" name="role" value="admin" onchange="updateRole()" <?= $role_selected === 'admin' ? 'checked' : ''; ?>>
            <span>Admin</span>
        </label>
        <label id="label-murid" class="<?= $role_selected === 'murid' ? 'active' : ''; ?>">
            <input type="radio" name="role" value="murid" onchange="updateRole()" <?= $role_selected === 'murid' ? 'checked' : ''; ?>>
            <span>Murid</span>
        </label>
    </div>

    <label id="label-username">Username</label>
    <input type="text" name="username" id="input-username" placeholder="Masukkan username" required>
    <p class="hint" id="hint-username" style="display:none;">Gunakan NIS kamu sebagai username</p>

    <label>Password</label>
    <input type="password" name="password" placeholder="Masukkan password" required>
    <p class="hint" id="hint-password" style="display:none;">Password default = NIS kamu</p>

    <button type="submit" name="login">Login</button>

    <p class="register-link" id="register-link" style="display:none;">
        Belum punya akun? <a href="daftar.php">Daftar di sini</a>
    </p>
</form>

<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>

<script>
    function updateRole() {
        const role = document.querySelector('input[name="role"]:checked').value;

        document.getElementById('label-admin').classList.toggle('active', role === 'admin');
        document.getElementById('label-murid').classList.toggle('active', role === 'murid');

        const isMurid = role === 'murid';
        document.getElementById('label-username').innerText = isMurid ? 'NIS' : 'Username';
        document.getElementById('input-username').placeholder = isMurid ? 'Masukkan NIS' : 'Masukkan username';
        document.getElementById('hint-username').style.display = isMurid ? 'block' : 'none';
        document.getElementById('hint-password').style.display = isMurid ? 'block' : 'none';
        document.getElementById('register-link').style.display = isMurid ? 'block' : 'none';
    }

    updateRole();
</script>

</body>
</html>
