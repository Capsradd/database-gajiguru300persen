<?php
require_once 'koneksi.php';

$status = $_GET['status'] ?? '';
$open_modal = $_GET['modal'] ?? '';
$edit_matkul = null;
$delete_matkul = null;

if ($is_logged_in && $conn) {
    if (isset($_POST['save_matkul'])) {
        $kodemk = trim($_POST['kodemk'] ?? '');
        $namamk = trim($_POST['namamk'] ?? '');
        $sks = trim($_POST['sks'] ?? '');

        $stmt = $conn->prepare('INSERT INTO tbl_matakuliah (kodemk, namamk, sks) VALUES (?, ?, ?)');

        if ($stmt) {
            $stmt->bind_param('sss', $kodemk, $namamk, $sks);
            $stmt->execute();
            $stmt->close();
            header('Location: matkul.php?status=added');
            exit;
        }

        $status = 'error';
    }

    if (isset($_POST['update_matkul'])) {
        $kodemk = trim($_POST['kodemk'] ?? '');
        $namamk = trim($_POST['namamk'] ?? '');
        $sks = trim($_POST['sks'] ?? '');

        $stmt = $conn->prepare('UPDATE tbl_matakuliah SET namamk = ?, sks = ? WHERE kodemk = ?');

        if ($stmt) {
            $stmt->bind_param('sss', $namamk, $sks, $kodemk);
            $stmt->execute();
            $stmt->close();
            header('Location: matkul.php?status=updated');
            exit;
        }

        $status = 'error';
    }

    if (isset($_POST['delete_matkul'])) {
        $kodemk = trim($_POST['kodemk'] ?? '');

        $stmt = $conn->prepare('DELETE FROM tbl_matakuliah WHERE kodemk = ?');

        if ($stmt) {
            $stmt->bind_param('s', $kodemk);
            $stmt->execute();
            $stmt->close();
            header('Location: matkul.php?status=deleted');
            exit;
        }

        $status = 'error';
    }

    $edit_kodemk = trim($_GET['edit'] ?? '');
    if ($edit_kodemk !== '') {
        $stmt = $conn->prepare('SELECT kodemk, namamk, sks FROM tbl_matakuliah WHERE kodemk = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $edit_kodemk);
            $stmt->execute();
            $result = $stmt->get_result();
            $edit_matkul = $result ? $result->fetch_assoc() : null;
            $stmt->close();
            $open_modal = 'edit';
        }
    }

    $delete_kodemk = trim($_GET['delete'] ?? '');
    if ($delete_kodemk !== '') {
        $stmt = $conn->prepare('SELECT kodemk, namamk, sks FROM tbl_matakuliah WHERE kodemk = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $delete_kodemk);
            $stmt->execute();
            $result = $stmt->get_result();
            $delete_matkul = $result ? $result->fetch_assoc() : null;
            $stmt->close();
            $open_modal = 'delete';
        }
    }
}

$query = null;
$total_matkul = 0;

$query = mysqli_query($conn, 'SELECT * FROM tbl_matakuliah ORDER BY kodemk ASC');

if ($query) {
    $total_matkul = mysqli_num_rows($query);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mata Kuliah - Unidb</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#111827',
                        primary: '#f3f4f6',
                        secondary: '#9ca3af',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-white text-gray-800 font-sans antialiased flex h-screen overflow-hidden">

    <?php if (!$is_logged_in): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white p-8 rounded-xl shadow-2xl w-96 max-w-[90%] border border-gray-100">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-black text-white rounded-lg mb-4 shadow-lg shadow-black/20">
                        <i class="fa-solid fa-database text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Database Login</h2>
                    <p class="text-sm text-gray-500 mt-1">Gunakan kredensial MySQL/MariaDB kamu</p>
                </div>

                <?php if (isset($error_msg) && $error_msg): ?>
                    <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-5 border border-red-100 flex gap-2 items-start">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <span><?php echo $error_msg; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">DB Username</label>
                        <input type="text" name="username" required placeholder="Contoh: root" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">DB Password</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tanpa password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all">
                    </div>
                    <button type="submit" name="login" class="w-full bg-black text-white font-medium py-2.5 px-4 rounded-lg hover:bg-gray-800 transition-all flex justify-center items-center gap-2">
                        <i class="fa-solid fa-plug"></i> Connect & Login
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php require_once 'sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-full bg-white overflow-hidden">
        <header class="h-16 flex items-center justify-between px-8 border-b border-gray-200 flex-shrink-0 bg-white">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Data Mata Kuliah</h1>
                <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                    <i class="fa-regular fa-clock"></i> Terakhir diperbarui: Baru saja
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Cari Mata Kuliah"
                        class="bg-gray-50 text-sm rounded-md pl-9 pr-4 py-2 w-64 focus:outline-none border border-gray-200"
                    >
                </div>
                <button class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 border border-transparent"><i class="fa-regular fa-circle-question"></i></button>
                <button class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 border border-transparent"><i class="fa-regular fa-bell"></i></button>
                <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden border border-gray-300 ml-2 cursor-pointer">
                    <img src="https://ui-avatars.com/api/?name=RaddinPratma&background=e5e7eb&color=1f2937" alt="Profile" class="w-full h-full object-cover">
                </div>
            </div>
        </header>

        <div class="flex-1 flex flex-col p-8 overflow-hidden min-h-0">
            <?php if ($status === 'added'): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                Data mata kuliah berhasil ditambahkan.
            </div>
            <?php elseif ($status === 'updated'): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                Data mata kuliah berhasil diperbarui.
            </div>
            <?php elseif ($status === 'deleted'): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                Data mata kuliah berhasil dihapus.
            </div>
            <?php elseif ($status === 'error'): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Proses gagal dijalankan.
            </div>
            <?php endif; ?>

            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <button class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium">
                        <i class="fa-solid fa-graduation-cap"></i> Program Studi
                        <i class="fa-solid fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                    <button class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-filter text-gray-400"></i> Semua Matakuliah
                        <i class="fa-solid fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.location.reload()" class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-rotate-right text-gray-400"></i> Refresh
                    </button>
                    <button type="button" onclick="openAddModal()" class="flex items-center gap-2 px-4 py-1.5 text-sm bg-black text-white rounded-md hover:bg-gray-800 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-plus"></i> Tambah Matkul
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600"><?php echo ($search !== '') ? 'Total Matakuliah' : 'Total Mata Kuliah'; ?></span>
                        <div class="w-8 h-8 rounded bg-blue-50 flex items-center justify-center text-blue-500">
                            <i class="fa-solid fa-book"></i>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Data pada tabel tbl_matakuliah</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold"><?php echo number_format($total_matkul); ?></span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">Aktif</span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Aksi Cepat</span>
                        <div class="w-8 h-8 rounded bg-green-50 flex items-center justify-center text-green-500">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Kelola data kurikulum matkul</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold">CRUD</span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-green-100 text-green-700">Ready</span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Status Koneksi</span>
                        <div class="w-8 h-8 rounded bg-purple-50 flex items-center justify-center text-purple-500">
                            <i class="fa-solid fa-database"></i>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Basisdata2026</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold">Online</span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-blue-100 text-blue-700">Connected</span>
                    </div>
                </div>
            </div>

            <div class="flex-1 flex flex-col min-h-0">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Daftar Mata Kuliah</h2>
                        <p class="text-xs text-gray-500">Data kode, nama, dan SKS dari database</p>
                    </div>
                </div>

                <div class="flex-1 min-h-0 border border-gray-200 rounded-lg overflow-auto bg-white">
                    <table class="w-full text-left border-collapse text-sm" id="matkulTable">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50/50">
                                <th class="font-medium text-gray-500 py-3 px-4 w-12 text-center">No</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Kode MK</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Nama Mata Kuliah</th>
                                <th class="font-medium text-gray-500 py-3 px-4 w-20 text-center">SKS</th>
                                <th class="font-medium text-gray-500 py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if ($query && (($query instanceof mysqli_result && $query->num_rows > 0) || (is_object($query) && method_exists($query, 'num_rows') && $query->num_rows > 0) || mysqli_num_rows($query) > 0)): ?>
                                <?php $no = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr class="hover:bg-gray-50 matkul-row">
                                    <td class="py-3 px-4 text-center text-gray-500 row-no"><?php echo $no++; ?></td>
                                    <td class="py-3 px-4 text-gray-500 font-mono text-xs font-kodemk"><?php echo htmlspecialchars($row['kodemk']); ?></td>
                                    <td class="py-3 px-4 text-gray-900 font-medium font-namamk"><?php echo htmlspecialchars($row['namamk']); ?></td>
                                    <td class="py-3 px-4 text-center text-gray-500 font-sks"><?php echo htmlspecialchars($row['sks']); ?></td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button" data-kodemk="<?php echo htmlspecialchars($row['kodemk'], ENT_QUOTES); ?>" data-namamk="<?php echo htmlspecialchars($row['namamk'], ENT_QUOTES); ?>" data-sks="<?php echo htmlspecialchars($row['sks'], ENT_QUOTES); ?>" onclick="openEditModalFromButton(this)" class="px-3 py-1.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-100">
                                                Edit
                                            </button>
                                            <button type="button" data-kodemk="<?php echo htmlspecialchars($row['kodemk'], ENT_QUOTES); ?>" data-namamk="<?php echo htmlspecialchars($row['namamk'], ENT_QUOTES); ?>" onclick="openDeleteModalFromButton(this)" class="px-3 py-1.5 rounded-md text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 border border-red-100">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr id="emptyRow">
                                <td colspan="5" class="py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-sm font-medium text-gray-500">
                                        Belum ada data mata kuliah.
                                    </p>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr id="noResultRow" class="hidden">
                                <td colspan="5" class="py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-magnifying-glass text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-sm font-medium text-gray-500">
                                        Mata kuliah tidak ditemukan.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="addModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Mata Kuliah</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Buat data mata kuliah baru.</p>
                </div>
                <button type="button" onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode MK</label>
                    <input type="text" name="kodemk" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Mata Kuliah</label>
                    <input type="text" name="namamk" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">SKS</label>
                    <input type="number" name="sks" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('addModal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" name="save_matkul" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Mata Kuliah</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui detail mata kuliah di tempat.</p>
                </div>
                <button type="button" onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode MK</label>
                    <input type="text" id="edit_kodemk" name="kodemk" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Mata Kuliah</label>
                    <input type="text" id="edit_namamk" name="namamk" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">SKS</label>
                    <input type="number" id="edit_sks" name="sks" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('editModal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" name="update_matkul" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Hapus Mata Kuliah</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Konfirmasi penghapusan data.</p>
                </div>
                <button type="button" onclick="closeModal('deleteModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form method="POST" class="p-6 space-y-5">
                <input type="hidden" id="delete_kodemk" name="kodemk">
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    Apakah kamu yakin ingin menghapus data mata kuliah <span id="delete_namamk" class="font-semibold"></span>?
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('deleteModal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" name="delete_matkul" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const openModalId = <?php echo json_encode($open_modal); ?>;
        const editMatkul = <?php echo json_encode($edit_matkul); ?>;
        const deleteMatkul = <?php echo json_encode($delete_matkul); ?>;

        function showModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openAddModal() {
            showModal('addModal');
        }

        function openEditModalFromButton(button) {
            document.getElementById('edit_kodemk').value = button.dataset.kodemk || '';
            document.getElementById('edit_namamk').value = button.dataset.namamk || '';
            document.getElementById('edit_sks').value = button.dataset.sks || '';
            showModal('editModal');
        }

        function openDeleteModalFromButton(button) {
            document.getElementById('delete_kodemk').value = button.dataset.kodemk || '';
            document.getElementById('delete_namamk').textContent = button.dataset.namamk || '';
            showModal('deleteModal');
        }

        if (openModalId === 'add') {
            showModal('addModal');
        }

        if (openModalId === 'edit') {
            if (editMatkul) {
                document.getElementById('edit_kodemk').value = editMatkul.kodemk || '';
                document.getElementById('edit_namamk').value = editMatkul.namamk || '';
                document.getElementById('edit_sks').value = editMatkul.sks || '';
            }
            showModal('editModal');
        }

        if (openModalId === 'delete') {
            if (deleteMatkul) {
                document.getElementById('delete_kodemk').value = deleteMatkul.kodemk || '';
                document.getElementById('delete_namamk').textContent = deleteMatkul.namamk || '';
            }
            showModal('deleteModal');
        }

        document.querySelectorAll('[id$="Modal"]').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        });

        document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();

        const rows = document.querySelectorAll('#matkulTable tbody tr.matkul-row');
        const noResultRow = document.getElementById('noResultRow');

        let visibleCount = 0;

        rows.forEach(row => {
            const kodemk = row.querySelector('.font-kodemk').textContent.toLowerCase();
            const namamk = row.querySelector('.font-namamk').textContent.toLowerCase();
            const sks = row.querySelector('.font-sks').textContent.toLowerCase();

            if (
                kodemk.includes(q) ||
                namamk.includes(q) ||
                sks.includes(q)
            ) {
                row.classList.remove('hidden');
                visibleCount++;

                row.querySelector('.row-no').textContent = visibleCount;
            } else {
                row.classList.add('hidden');
            }
        });

        if (visibleCount === 0 && rows.length > 0) {
            noResultRow.classList.remove('hidden');
        } else {
            noResultRow.classList.add('hidden');
        }
    });
    </script>

</body>
</html>