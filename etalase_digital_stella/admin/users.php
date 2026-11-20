<?php
require_once __DIR__ . '/../config/kuis_db.php';
require_once __DIR__ . '/../middleware/auth_check.php';

check_role(['super_admin']);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (isset($_POST['tambah_admin'])) {
        $nama     = trim($_POST['nama'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($nama) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $error = 'Isi data dengan benar. Password minimal 8 karakter.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'admin')");
                $stmt->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT)]);
                header('Location: users.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Email sudah digunakan.';
            }
        }
    } elseif (isset($_POST['hapus_user'])) {
        $id = (int)($_POST['user_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role <> 'super_admin' AND id <> ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        header('Location: users.php');
        exit;
    }
}

$users = $pdo->query("SELECT id, nama, email, role FROM users ORDER BY role, nama")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pengguna</title>
</head>
<body>
    <header class="navbar">
        <a class="logo" href="../index.php">Etalase Digital Stella</a>
        <a class="btn btn-light" href="index.php">Dashboard</a>
    </header>

    <main class="container admin-wrap">
        <p class="eyebrow">SUPER ADMIN</p>
        <h1>Manajemen User</h1>
        <p class="muted">Tambah akun admin dan kelola pengguna sistem.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <section class="admin-card">
            <h2>Tambah Admin Baru</h2>
            <form method="post" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= e(generate_csrf()) ?>">
                
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password (Min. 8 Karakter)</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" name="tambah_admin" class="btn btn-primary">Simpan Admin</button>
            </form>
        </section>

        <section class="admin-card">
            <h2>Daftar Pengguna</h2>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= e($u['nama']) ?></td>
                                <td><?= e($u['email']) ?></td>
                                <td><span class="role-pill"><?= e($u['role']) ?></span></td>
                                <td>
                                    <?php if ($u['role'] !== 'super_admin' && $u['id'] !== $_SESSION['user_id']): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= e(generate_csrf()) ?>">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <button type="submit" name="hapus_user" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>