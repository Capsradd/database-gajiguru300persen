<?php
include 'koneksi.php';

if(isset($_POST['simpan'])){

    $nid = $_POST['nid'];
    $namados = $_POST['namados'];

    mysqli_query($conn,
    "INSERT INTO tbl_dosen (nid, namados)
    VALUES ('$nid','$namados')");

    header("Location: dosen.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6">

    <div class="bg-white p-6 rounded shadow max-w-lg mx-auto">

        <h1 class="text-2xl font-bold mb-4">
            Tambah Dosen
        </h1>

        <form method="POST">

            <div class="mb-4">
                <label>NID</label>
                <input type="text"
                       name="nid"
                       class="w-full border p-2 rounded"
                       required>
            </div>

            <div class="mb-4">
                <label>Nama Dosen</label>
                <input type="text"
                       name="namados"
                       class="w-full border p-2 rounded"
                       required>
            </div>

            <button type="submit"
                    name="simpan"
                    class="bg-green-500 text-white px-4 py-2 rounded">
                Simpan
            </button>

            <a href="dosen.php"
               class="bg-gray-500 text-white px-4 py-2 rounded">
               Kembali
            </a>

        </form>

    </div>

</div>

</body>
</html>