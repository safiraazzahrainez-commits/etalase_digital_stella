# Etalase Digital Stella

## Cara menjalankan di XAMPP
1. Ekstrak folder `etalase-digital-stella` ke `C:\xampp\htdocs\`.
2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih tab **Import**, lalu impor file `schema.sql` dari folder proyek. File ini membuat database `db_etalase_stella`.
5. Pastikan pengaturan database di `config/kuis_db.php` sesuai dengan XAMPP. Pengaturan bawaan: host `localhost`, user `root`, password kosong.
6. Buat akun siswa melalui `http://localhost/etalase-digital-stella/auth/register.php`.
7. Buka `http://localhost/etalase-digital-stella/auth/login.php` untuk login.

## Membuat akun Super Admin pertama
Registrasi melalui halaman website hanya membuat role `siswa`. Untuk membuat Super Admin pertama:
1. Daftar akun biasa melalui halaman Register.
2. Buka phpMyAdmin, pilih database `db_etalase_stella`, lalu buka tab SQL.
3. Jalankan perintah berikut dan ganti email sesuai email akun yang baru didaftarkan:

```sql
UPDATE users SET role = 'super_admin' WHERE email = 'emailkamu@example.com';
```

4. Logout lalu login kembali agar sesi memuat role baru.

## Hak akses
- **Super Admin:** mengelola akun pengguna, mengelola semua karya, dan menambah karya.
- **Admin:** mengelola semua karya dan menambah karya; tidak dapat mengelola akun.
- **Siswa:** melihat semua karya, menambah karya, serta mengedit/menghapus karya miliknya sendiri.

## Catatan
- Gambar yang diterima: JPG, PNG, WEBP, maksimal 3 MB.
- Password disimpan menggunakan `password_hash()` dan diverifikasi dengan `password_verify()`.
- Folder `uploads/` harus dapat ditulisi oleh PHP.
- Nama database mengikuti panduan resolusi: `db_etalase_stella`.
