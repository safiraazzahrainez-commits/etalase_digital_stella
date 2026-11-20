<?php
require_once __DIR__ . '/config/kuis_db.php';
require_once __DIR__ . '/middleware/auth_check.php';
check_login();

$stmt = $pdo->query(
    "SELECT p.*, u.nama AS pembuat
    FROM projects p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC"
);
$projects = $stmt->fetchAll();
$role = $_SESSION['role'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etalase Digital Stella</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <header class="navbar">
        <a class="logo" href="index.php">✦ Etalase Digital Stella</a>
        <nav class="navbar-user">
            <span>
                Halo, <b><?= e($_SESSION['nama']) ?></b>
                <small class="role"><?= e(str_replace('_', ' ', $role)) ?></small>
            </span>
            <a class="btn btn-primary" href="project_tambah.php">+ Tambah Karya</a>
            <?php if ($role === 'super_admin'): ?>
                <a class="btn btn-light" href="admin/users.php">Kelola User</a>
            <?php endif; ?>
            <a class="btn btn-danger" href="auth/logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <section class="hero">
            <p class="eyebrow">KREATIVITAS • KOLABORASI • INOVASI</p>
            <h1>Etalase Karya Digital<br><span>Siswa Stella</span></h1>
            <p>Temukan ide, eksplorasi karya, dan inspirasi dari siswa.</p>
        </section>

        <div class="section-head">
            <div>
                <h2>Karya Terbaru</h2>
                <p class="muted"><?= count($projects) ?> karya ditampilkan</p>
            </div>
        </div>

        <?php if (!$projects): ?>
            <div class="empty-state">
                <div>✧</div>
                <h3>Belum ada karya</h3>
                <p>Jadilah yang pertama menampilkan project di etalase.</p>
                <a class="btn btn-primary" href="project_tambah.php">Tambah karya pertama</a>
            </div>
        <?php else: ?>
            <section class="project-grid">
                <?php foreach ($projects as $p): ?>
                    <article class="project-card">
                        <img
                            class="project-image"
                            src="uploads/<?= e($p['gambar']) ?>"
                            alt="<?= e($p['nama_project']) ?>"
                        >
                        <div class="project-content">
                            <span class="tag">PROJECT</span>
                            <h3><?= e($p['nama_project']) ?></h3>
                            <p class="byline">Oleh <?= e($p['pembuat']) ?></p>
                            <p class="description"><?= nl2br(e($p['deskripsi'])) ?></p>

                            <div class="card-actions">
                                <a
                                    class="btn btn-primary"
                                    href="<?= e($p['link_project']) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >Lihat Project ↗</a>
                                <?php if (can_manage_project($p)): ?>
                                    <a class="btn btn-light" href="project_edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
                                    <form
                                        method="post"
                                        action="project_hapus.php"
                                        onsubmit="return confirm('Yakin ingin menghapus karya ini?')"
                                    >
                                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e(csrf_token()) ?>"
                                        >
                                        <button class="btn btn-danger" type="submit">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer class="footer">© <?= date('Y') ?> Etalase Digital Stella</footer>
</body>
</html>
