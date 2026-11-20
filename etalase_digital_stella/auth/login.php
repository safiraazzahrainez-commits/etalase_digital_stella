<?php
session_start();
require_once __DIR__ . '/../config/kuis_db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, nama, email, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        header('Location: ../index.php');
        exit;
    }
    $error = 'Email atau password tidak sesuai.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Etalase Digital Stella</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body class="auth-wrapper">
    <main class="auth-card">
        <div class="auth-brand">
            <img src="../assets/telkom-schools-logo.png" alt="Logo Telkom Schools">
            <span>Telkom<br>Schools</span>
        </div>
        <h1>Selamat Datang</h1>
        <p class="muted">Masuk ke Etalase Digital Stella</p>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Email</label>
            <input
                class="form-control"
                type="email"
                name="email"
                placeholder="nama@email.com"
                required
            >

            <label>Password</label>
            <input
                class="form-control"
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

            <button class="btn btn-primary full" type="submit">Login</button>
        </form>

        <p class="auth-foot">
            Belum punya akun siswa? <a href="register.php">Daftar di sini</a>
        </p>
    </main>
</body>
</html>
