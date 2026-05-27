<?php
session_start();

// Hapus semua variabel session
$_SESSION = [];
session_unset();

// Hancurkan session
session_destroy();

// Hapus cookie session jika ada (mencegah nyangkut di browser)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Kembalikan ke halaman index
header("Location: index.php");
exit;
?>