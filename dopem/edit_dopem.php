<?php
include __DIR__ . '/../koneksi.php';

$nim = $_GET['nim'];

$data = mysqli_query($conn,
    "SELECT * FROM tbl_dopem WHERE nim='$nim'");

if (!$data) {
    die('Query error: ' . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){

    $nid = $_POST['nid'];

    mysqli_query($conn,
    "UPDATE tbl_dopem
    SET nid='$nid'
    WHERE nim='$nim'");

    header("Location: dopem.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Dosen Pembimbing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6">

    <div class="bg-white p-6 rounded shadow max-w-lg mx-auto">

        <h1 class="text-2xl font-bold mb-4">
            Edit Dosen Pembimbing
        </h1>

        <form method="POST">

            <div class="mb-4">
                <label>NIM</label>
                <input type="text"
                       value="<?php echo $row['nim']; ?>"
                       class="w-full border p-2 rounded bg-gray-100"
                       readonly>
            </div>

            <div class="mb-4">
                <label>NID</label>
                <input type="text"
                       name="nid"
                       value="<?php echo $row['nid']; ?>"
                       class="w-full border p-2 rounded"
                       required>
            </div>

            <button type="submit"
                    name="update"
                    class="bg-blue-500 text-white px-4 py-2 rounded">
                Update
            </button>

            <a href="dopem.php"
               class="bg-gray-500 text-white px-4 py-2 rounded">
               Kembali
            </a>

        </form>

    </div>

</div>

</body>
</html>