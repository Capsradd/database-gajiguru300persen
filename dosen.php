<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6">

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Data Dosen</h1>

        <table class="min-w-full border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">NID</th>
                    <th class="border px-4 py-2">Nama Dosen</th>
                </tr>
            </thead>
            <tbody>

            <?php
            $query = mysqli_query($conn, "SELECT * FROM tbl_dosen");

            while($row = mysqli_fetch_assoc($query)){
            ?>

                <tr>
                    <td class="border px-4 py-2"><?php echo $row['nid']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['namados']; ?></td>
                </tr>

            <?php } ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>