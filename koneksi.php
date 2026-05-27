<?php
// Mulai session
session_start();

// Konfigurasi host & nama database
$db_host = 'localhost';
$db_name = 'basisdata2026'; // Nama database kamu
$error_msg = '';

// Proses Login jika ada POST request dari form login
if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    try {
        // Mencoba koneksi ke server MySQL/MariaDB dengan user & pass yang diinput
        $conn = @new mysqli($db_host, $user, $pass);
        
        if ($conn->connect_error) {
            $error_msg = "Login MySQL gagal: Username atau password salah.";
        } else {
            // Cek apakah database ada
            if (!$conn->select_db($db_name)) {
                $error_msg = "Login berhasil, tapi database '$db_name' tidak ditemukan. Silakan buat dahulu di phpMyAdmin.";
            } else {
                // Sukses terhubung ke database
                $_SESSION['db_user'] = $user;
                $_SESSION['db_pass'] = $pass;
                
                // Redirect agar form tidak ter-submit ulang saat direfresh
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    } catch (Exception $e) {
        $error_msg = "Koneksi error: " . $e->getMessage();
    }
}

// Cek status login dari session
$is_logged_in = isset($_SESSION['db_user']);
$conn = null;

if ($is_logged_in) {
    // Kalau sudah login, buat koneksi aktif untuk dipakai eksekusi query (ambil data)
    $conn = @new mysqli($db_host, $_SESSION['db_user'], $_SESSION['db_pass'], $db_name);
    
    // Jika ternyata user dihapus/password diganti di tengah jalan
    if ($conn->connect_error) {
        session_unset();
        session_destroy();
        $is_logged_in = false;
        $error_msg = "Sesi tidak valid / Password berubah. Silakan login kembali.";
    }
}
?>