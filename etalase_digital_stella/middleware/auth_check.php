<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_login(): void {
    if (empty($_SESSION['user_id'])) {
        $scriptDirectory = basename(dirname($_SERVER['SCRIPT_NAME']));
        $loginPath = $scriptDirectory === 'admin'
            ? '../auth/login.php'
            : 'auth/login.php';

        header('Location: ' . $loginPath);
        exit;
    }
}

function check_role(array $roles = []): void {
    check_login();
    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
        http_response_code(403);
        exit('Akses ditolak: Anda tidak memiliki hak akses.');
    }
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sent = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $sent)) {
            http_response_code(419);
            exit('Permintaan tidak valid. Muat ulang halaman lalu coba lagi.');
        }
    }
}

function can_manage_project(array $project): bool {
    $role = $_SESSION['role'] ?? '';
    return in_array($role, ['super_admin', 'admin'], true)
        || (int)$project['user_id'] === (int)($_SESSION['user_id'] ?? 0);
}

function save_uploaded_image(array $file): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('Gambar gagal diunggah atau ukurannya melebihi 3 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];
    if (
        !isset($allowed[$mime])
        || getimagesize($file['tmp_name']) === false
    ) {
        throw new RuntimeException('Format gambar harus JPG, PNG, atau WEBP.');
    }

    $dir = dirname(__DIR__) . '/uploads/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
        throw new RuntimeException('Gambar tidak dapat disimpan.');
    }
    return $name;
}

function delete_project_image(?string $name): void {
    if (!$name || basename($name) !== $name) {
        return;
    }

    $path = dirname(__DIR__) . '/uploads/' . $name;
    if (is_file($path)) {
        unlink($path);
    }
}
