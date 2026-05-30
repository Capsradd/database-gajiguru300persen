<?php
include 'koneksi.php';

$nid = $_GET['nid'];

$data = mysqli_query($conn,
"SELECT * FROM tbl_dosen WHERE nid='$nid'");

$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){

    $namados = $_POST['namados'];

    mysqli_query($conn,
    "UPDATE tbl_dosen
    SET namados='$namados'
    WHERE nid='$nid'");

    header("Location: dosen.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6">

    <div class="bg-white p-6 rounded shadow max-w-lg mx-auto">

        <h1 class="text-2xl font-bold mb-4">
            Edit Dosen
        </h1>

        <form method="POST">

            <div class="mb-4">
                <label>NID</label>
                <input type="text"
                       value="<?php echo $row['nid']; ?>"
                       class="w-full border p-2 rounded bg-gray-100"
                       readonly>
            </div>

            <div class="mb-4">
                <label>Nama Dosen</label>
                <input type="text"
                       name="namados"
                       value="<?php echo $row['namados']; ?>"
                       class="w-full border p-2 rounded"
                       required>
            </div>

            <button type="submit"
                    name="update"
                    class="bg-blue-500 text-white px-4 py-2 rounded">
                Update
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