<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6">

    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">
                Data Dosen
            </h1>

            <a href="tambah_dosen.php"
               class="bg-green-500 text-white px-4 py-2 rounded">
                + Tambah Dosen
            </a>
        </div>

        <table class="min-w-full border border-gray-200">

            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">NID</th>
                    <th class="border px-4 py-2">Nama Dosen</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php
            $query = mysqli_query($conn, "SELECT * FROM tbl_dosen");

            while($row = mysqli_fetch_assoc($query)){
            ?>

            <tr>
                <td class="border px-4 py-2">
                    <?php echo $row['nid']; ?>
                </td>

                <td class="border px-4 py-2">
                    <?php echo $row['namados']; ?>
                </td>

                <td class="border px-4 py-2">

                    <a href="edit_dosen.php?nid=<?php echo $row['nid']; ?>"
                       class="bg-blue-500 text-white px-3 py-1 rounded">
                        Edit
                    </a>

                    <a href="hapus_dosen.php?nid=<?php echo $row['nid']; ?>"
                       onclick="return confirm('Yakin hapus data?')"
                       class="bg-red-500 text-white px-3 py-1 rounded ml-2">
                        Hapus
                    </a>

                </td>
            </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>