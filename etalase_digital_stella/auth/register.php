<?php
require_once __DIR__ . '/../config/kuis_db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        $nama === ''
        || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 8
    ) {
        $error = 'Lengkapi data dengan benar. Password minimal 8 karakter.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (nama, email, password, role)
                VALUES (?, ?, ?, 'siswa')"
            );
            $stmt->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Email sudah digunakan. Gunakan email lain.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | Etalase Digital Stella</title>
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

        <form method="post" class="auth-form">
            <label for="nama">Nama lengkap</label>
            <input
                class="form-control"
                type="text"
                id="nama"
                name="nama"
                placeholder="Nama lengkap"
                autocomplete="name"
                required
                maxlength="100"
            >

            <label for="email">Email</label>
            <input
                class="form-control"
                type="email"
                id="email"
                name="email"
                placeholder="nama@email.com"
                autocomplete="email"
                required
            >

            <label for="password">Password (minimal 8 karakter)</label>
            <input
                class="form-control"
                type="password"
                id="password"
                name="password"
                placeholder="Buat password"
                autocomplete="new-password"
                minlength="8"
                required
            >

            <button class="btn btn-primary full" type="submit">Daftar</button>
        </form>

        <p class="auth-foot">
            Sudah punya akun? <a href="login.php">Login</a>
        </p>
    </main>
</body>
</html>
