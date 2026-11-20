<?php
require_once __DIR__ . '/config/kuis_db.php';
require_once __DIR__ . '/middleware/auth_check.php';
check_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    http_response_code(404);
    exit('Karya tidak ditemukan.');
}

if (!can_manage_project($project)) {
    http_response_code(403);
    exit('Akses ditolak.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $nama = trim($_POST['nama_project'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $link = trim($_POST['link_project'] ?? '');
        if (
            $nama === ''
            || $deskripsi === ''
            || !filter_var($link, FILTER_VALIDATE_URL)
            || !in_array(parse_url($link, PHP_URL_SCHEME), ['http', 'https'], true)
        ) {
            throw new RuntimeException('Periksa kembali nama, deskripsi, dan link project.');
        }
        $gambar = save_uploaded_image($_FILES['gambar'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
        $gambarFinal = $gambar ?: $project['gambar'];
        $up = $pdo->prepare(
            'UPDATE projects
            SET nama_project = ?, deskripsi = ?, link_project = ?, gambar = ?
            WHERE id = ?'
        );
        $up->execute([$nama, $deskripsi, $link, $gambarFinal, $id]);

        if ($gambar) {
            delete_project_image($project['gambar']);
        }

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
    <title>Edit Karya</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
    <main class="form-page">
        <a href="index.php" class="back-link">← Kembali ke etalase</a>
        <section class="form-card">
            <p class="eyebrow">ETALASE DIGITAL STELLA</p>
            <h1>Edit Karya</h1>

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
                    value="<?= e($_POST['nama_project'] ?? $project['nama_project']) ?>"
                >

                <label>Deskripsi</label>
                <textarea class="form-control" name="deskripsi" rows="5" required><?= e($_POST['deskripsi'] ?? $project['deskripsi']) ?></textarea>

                <label>Link project</label>
                <input
                    class="form-control"
                    type="url"
                    name="link_project"
                    required
                    value="<?= e($_POST['link_project'] ?? $project['link_project']) ?>"
                >

                <label>Gambar saat ini</label>
                <img
                    class="preview"
                    src="uploads/<?= e($project['gambar']) ?>"
                    alt="Gambar project"
                >

                <label>Ganti gambar (opsional, JPG/PNG/WEBP maks. 3 MB)</label>
                <input
                    class="form-control"
                    type="file"
                    name="gambar"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <button class="btn btn-primary full">Simpan Perubahan</button>
            </form>
        </section>
    </main>
</body>
</html>
