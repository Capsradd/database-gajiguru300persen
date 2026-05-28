---
name: db-kampus-guide
description: "Panduan konteks untuk repo DB Kampus: struktur file, alur login database, aturan kerja, dan do/don't saat mengedit proyek."
---

# DB Kampus Agent Guide

Kamu bekerja di project PHP sederhana di folder `db-kampus`. Tujuan utama proyek ini adalah menampilkan dashboard kampus dan menyediakan SQL console dengan login database langsung ke MySQL/MariaDB.

## Gambaran Struktur

- `index.php`: halaman utama dashboard dan form login database.
- `console.php`: SQL console untuk menjalankan query ke database aktif.
- `koneksi.php`: pusat konfigurasi koneksi dan session login database.
- `sidebar.php`: komponen sidebar dan navigasi.
- `404.php`: halaman fallback untuk menu yang belum tersedia.
- `proses_logout.php`: menghapus session dan mengembalikan user ke halaman awal.
- `image/logo.png`: logo kecil yang dipakai di sidebar.
- `image/readme-logo.png`: logo yang dipakai untuk README.

## Alur Kerja Aplikasi

- Aplikasi meminta username dan password MySQL/MariaDB lewat form login.
- `koneksi.php` mencoba koneksi ke host database lalu memilih database default.
- Jika login berhasil, credential disimpan di session agar halaman lain bisa mengakses database yang sama.
- `console.php` hanya boleh dipakai saat session login aktif.
- `proses_logout.php` menghapus session sepenuhnya.

## Do

- Baca `koneksi.php` dulu sebelum mengubah perilaku login, session, atau database.
- Pertahankan gaya UI yang sudah ada: Tailwind CDN, Font Awesome, layout dashboard bersih.
- Jaga perubahan tetap kecil dan fokus ke file yang memang bertanggung jawab.
- Update README kalau alur setup, nama database, atau lokasi file penting berubah.
- Gunakan `image/readme-logo.png` untuk logo README dan `image/logo.png` untuk logo sidebar.
- Pastikan semua output user-facing aman dari XSS dengan `htmlspecialchars` saat menampilkan data dinamis.

## Don't

- Jangan simpan password database hardcoded di file kalau tidak memang diperlukan.
- Jangan ubah session flow tanpa alasan jelas.
- Jangan menghapus file gambar logo tanpa pengganti yang jelas.
- Jangan refactor seluruh halaman kalau cukup patch kecil.
- Jangan membuat asumsi nama database atau host berbeda tanpa cek `koneksi.php`.
- Jangan mengubah query SQL di `console.php` kecuali memang diminta.

## Catatan Database

- Default database saat ini adalah `basisdata2026`.
- Kalau environment user berbeda, ubah nilai `db_host` dan `db_name` di `koneksi.php`.
- Username dan password database diisi saat login, jadi jangan dipindahkan ke hardcode kecuali diminta.
- Jika database belum ada, aplikasi akan menampilkan error bahwa database tidak ditemukan.

## Saat Diminta Mengedit

- Prioritaskan file yang paling dekat dengan perilaku yang diminta.
- Kalau diminta ubah tampilan, cek apakah cukup di `index.php`, `console.php`, atau `sidebar.php`.
- Kalau diminta ubah koneksi atau login, fokus ke `koneksi.php`.
- Kalau diminta ubah navigasi, fokus ke `sidebar.php`.
- Kalau diminta ubah logo atau dokumentasi, cek folder `image/` dan `README.md`.
