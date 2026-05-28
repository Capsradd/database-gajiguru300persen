# DB Kampus

![Logo](image/readme-logo.png)

Aplikasi PHP sederhana untuk menampilkan dashboard data kampus dengan login langsung ke MySQL/MariaDB. Project ini memakai koneksi database dari file `koneksi.php` dan menampilkan halaman utama di `index.php`.

## Fitur

- Login database langsung dari form di aplikasi
- Dashboard kampus sederhana
- Halaman 404 khusus
- Logout session

## Kebutuhan

- PHP 7.4+ atau lebih baru
- MySQL / MariaDB
- Web server seperti Apache atau Nginx
- XAMPP, Laragon, atau stack lokal lain juga bisa dipakai

## Cara Menjalankan

### 1. Taruh project di web server

Kalau pakai Linux dengan Apache, letakkan folder project di:

```bash
/var/www/html/db-kampus
```

### 2. Jalankan web server dan database

Pastikan service web server dan MySQL/MariaDB sudah aktif.

Contoh jika pakai XAMPP:

- Start Apache
- Start MySQL

### 3. Buat database

Secara default aplikasi memakai database bernama:

```text
basisdata2026
```

Kalau database itu belum ada, buat dulu lewat phpMyAdmin atau tool database lain, lalu import tabel yang dibutuhkan oleh aplikasi kamu.

### 4. Buka aplikasi

Akses lewat browser:

```text
http://localhost/db-kampus
```

### 5. Login database

Di halaman login, isi:

- Username database MySQL/MariaDB
- Password database MySQL/MariaDB

Setelah login berhasil, aplikasi akan menyimpan session dan langsung masuk ke dashboard.

## Cara Mengubah Database

Kalau nama database, host, atau kredensial di tempat kamu berbeda, edit file `koneksi.php`.

Bagian yang biasanya perlu diubah:

```php
$db_host = 'localhost';
$db_name = 'basisdata2026';
```

Penjelasan:

- `db_host` = alamat server database, misalnya `localhost` atau IP server lain
- `db_name` = nama database yang dipakai aplikasi

Kalau username/password MySQL berbeda, kamu tidak perlu hardcode di file. Aplikasi akan meminta login lewat form.

Yang perlu diperhatikan:

- Username dan password yang diisi di form harus punya akses ke database target
- Database target harus sudah ada sebelum login
- Kalau database gagal ditemukan, aplikasi akan menampilkan pesan error

### Contoh skenario

Kalau database kamu bernama `kampus_prod`, ubah:

```php
$db_name = 'kampus_prod';
```

Kalau database ada di server lain, ubah:

```php
$db_host = '192.168.1.10';
```

## Logout

Untuk keluar dari session login, buka file:

```text
proses_logout.php
```

Biasanya file ini akan menghapus session login lalu mengarahkan kembali ke halaman awal.