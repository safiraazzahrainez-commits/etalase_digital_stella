<?php
require_once __DIR__ . '/../config/kuis_db.php';
require_once __DIR__ . '/../middleware/auth_check.php';

check_role(['super_admin', 'admin']);

$jumlahProject = (int)$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$jumlahSiswa   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='siswa'")->fetchColumn();
$jumlahAdmin   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
</head>
<body>
    <header class="navbar">
        <a class="logo" href="../index.php">Etalase Digital Stella</a>
        <a class="btn btn-light" href="../index.php">Lihat Website</a>
    </header>

    <main class="container admin-wrap">
        <p class="eyebrow">PANEL <?= e(strtoupper(str_replace('_', ' ', $_SESSION['role']))) ?></p>
        <h1>Dashboard</h1>

        <div class="stats">
            <div class="stat">
                <span>Total Karya</span>
                <strong><?= $jumlahProject ?></strong>
            </div>
            <div class="stat">
                <span>Jumlah Siswa</span>
                <strong><?= $jumlahSiswa ?></strong>
            </div>
            <div class="stat">
                <span>Jumlah Admin</span>
                <strong><?= $jumlahAdmin ?></strong>
            </div>
        </div>

        <a class="btn btn-primary" href="../project_tambah.php">+ Tambah Karya</a>

        <?php if ($_SESSION['role'] === 'super_admin'): ?>
            <a class="btn btn-light" href="users.php">Kelola User</a>
        <?php endif; ?>
    </main>
</body>
</html> 