<?php
require_once __DIR__ . '/config/kuis_db.php';
require_once __DIR__ . '/middleware/auth_check.php';
check_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();

if ($project) {
    if (!can_manage_project($project)) {
        http_response_code(403);
        exit('Akses ditolak.');
    }

    $del = $pdo->prepare('DELETE FROM projects WHERE id = ?');
    $del->execute([$id]);
    delete_project_image($project['gambar']);
}

header('Location: index.php');
exit;
