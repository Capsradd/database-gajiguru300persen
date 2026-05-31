<?php

include __DIR__ . '/../koneksi.php';

$nim = isset($_GET['nim']) ? $_GET['nim'] : null;

if (!$nim) {
    die('Parameter nim tidak ditemukan.');
}

$result = mysqli_query($conn,
"DELETE FROM tbl_dopem
WHERE nim='$nim'");

if (!$result) {
    die('Query error: ' . mysqli_error($conn));
}

header("Location: dopem.php");
exit;

?>