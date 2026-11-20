CREATE DATABASE IF NOT EXISTS db_etalase_stella
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_etalase_stella;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin','admin','siswa') NOT NULL DEFAULT 'siswa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_project VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    gambar VARCHAR(255) NOT NULL,
    link_project VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_projects_user FOREIGN KEY (user_id)
      REFERENCES users(id) ON DELETE CASCADE
);

-- Buat akun super admin melalui langkah di README.md.
