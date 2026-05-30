<?php

include 'koneksi.php';

$nid = $_GET['nid'];

mysqli_query($conn,
"DELETE FROM tbl_dosen
WHERE nid='$nid'");

header("Location: dosen.php");

?>