<?php
require_once __DIR__ . '/config/kuis_db.php';
require_once __DIR__ . '/middleware/auth_check.php';
check_login();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nama = trim($_POST['nama_project'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $link = trim($_POST['link_project'] ?? '');
    try {
        if (
            $nama === ''
            || $deskripsi === ''
            || !filter_var($link, FILTER_VALIDATE_URL)
            || !in_array(parse_url($link, PHP_URL_SCHEME), ['http', 'https'], true)
        ) {
            throw new RuntimeException('Isi semua data dan gunakan link project yang valid (http/https).');
        }
        if (empty($_FILES['gambar']['name'])) {
            throw new RuntimeException('Gambar project wajib diunggah.');
        }
        $gambar = save_uploaded_image($_FILES['gambar']);
        $stmt = $pdo->prepare(
            'INSERT INTO projects (user_id, nama_project, deskripsi, gambar, link_project)
            VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$_SESSION['user_id'], $nama, $deskripsi, $gambar, $link]);
        header('Location: index.php');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Karya</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
    <main class="form-page">
        <a href="index.php" class="back-link">← Kembali ke etalase</a>
        <section class="form-card">
            <p class="eyebrow">ETALASE DIGITAL STELLA</p>
            <h1>Tambah Karya Baru</h1>
            <p class="muted">Ceritakan project terbaik yang ingin kamu bagikan.</p>

            <?php if ($error): ?>
                <div class="alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <label>Nama project</label>
                <input
                    class="form-control"
                    name="nama_project"
                    maxlength="150"
                    required
                    value="<?= e($_POST['nama_project'] ?? '') ?>"
                >

                <label>Deskripsi</label>
                <textarea class="form-control" name="deskripsi" rows="5" required><?= e($_POST['deskripsi'] ?? '') ?></textarea>

                <label>Link project</label>
                <input
                    class="form-control"
                    type="url"
                    name="link_project"
                    placeholder="https://..."
                    required
                    value="<?= e($_POST['link_project'] ?? '') ?>"
                >

                <label>Gambar project (JPG, PNG, WEBP; maks. 3 MB)</label>
                <input
                    class="form-control"
                    type="file"
                    name="gambar"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    required
                    id="gambar"
                >

                <img id="preview" class="preview" alt="Preview gambar" hidden>
                <button class="btn btn-primary full">Simpan Karya</button>
            </form>
        </section>
    </main>

<script>
document.getElementById('gambar').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const img = document.getElementById('preview');

    if (file) {
        img.src = URL.createObjectURL(file);
        img.hidden = false;
    } else {
        img.hidden = true;
    }
});
</script>
</body>
</html>
