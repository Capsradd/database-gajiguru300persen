<?php
require_once 'koneksi.php';

$status = $_GET['status'] ?? '';
$open_modal = $_GET['modal'] ?? '';
$edit_dosen = null;
$delete_dosen = null;

if ($is_logged_in && $conn) {
    if (isset($_POST['save_dosen'])) {
        $nid = trim($_POST['nid'] ?? '');
        $namados = trim($_POST['namados'] ?? '');

        $stmt = $conn->prepare('INSERT INTO tbl_dosen (nid, namados) VALUES (?, ?)');

        if ($stmt) {
            $stmt->bind_param('ss', $nid, $namados);
            $stmt->execute();
            $stmt->close();
            header('Location: dosen.php?status=added');
            exit;
        }

        $status = 'error';
    }

    if (isset($_POST['update_dosen'])) {
        $nid = trim($_POST['nid'] ?? '');
        $namados = trim($_POST['namados'] ?? '');

        $stmt = $conn->prepare('UPDATE tbl_dosen SET namados = ? WHERE nid = ?');

        if ($stmt) {
            $stmt->bind_param('ss', $namados, $nid);
            $stmt->execute();
            $stmt->close();
            header('Location: dosen.php?status=updated');
            exit;
        }

        $status = 'error';
    }

    if (isset($_POST['delete_dosen'])) {
        $nid = trim($_POST['nid'] ?? '');

        $stmt = $conn->prepare('DELETE FROM tbl_dosen WHERE nid = ?');

        if ($stmt) {
            $stmt->bind_param('s', $nid);
            $stmt->execute();
            $stmt->close();
            header('Location: dosen.php?status=deleted');
            exit;
        }

        $status = 'error';
    }

    $edit_nid = trim($_GET['edit'] ?? '');
    if ($edit_nid !== '') {
        $stmt = $conn->prepare('SELECT nid, namados FROM tbl_dosen WHERE nid = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $edit_nid);
            $stmt->execute();
            $result = $stmt->get_result();
            $edit_dosen = $result ? $result->fetch_assoc() : null;
            $stmt->close();
            $open_modal = 'edit';
        }
    }

    $delete_nid = trim($_GET['delete'] ?? '');
    if ($delete_nid !== '') {
        $stmt = $conn->prepare('SELECT nid, namados FROM tbl_dosen WHERE nid = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $delete_nid);
            $stmt->execute();
            $result = $stmt->get_result();
            $delete_dosen = $result ? $result->fetch_assoc() : null;
            $stmt->close();
            $open_modal = 'delete';
        }
    }
}

$query = null;
$total_dosen = 0;

if ($is_logged_in && $conn) {
    $query = mysqli_query($conn, "SELECT * FROM tbl_dosen ORDER BY nid ASC");

    if ($query) {
        $total_dosen = mysqli_num_rows($query);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen - Unidb</title>
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

            <?php if ($error_msg): ?>
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
                <h1 class="text-lg font-semibold text-gray-900">Data Dosen</h1>
                <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                    <i class="fa-regular fa-clock"></i> Terakhir diperbarui: Baru saja
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Search Dosen" class="bg-gray-50 text-sm rounded-md pl-9 pr-4 py-2 w-64 focus:outline-none border border-gray-200">
                </div>
                <button class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 border border-transparent"><i class="fa-regular fa-circle-question"></i></button>
                <button class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 border border-transparent"><i class="fa-regular fa-bell"></i></button>
                <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden border border-gray-300 ml-2 cursor-pointer">
                    <img src="https://ui-avatars.com/api/?name=User&background=random" alt="Profile" class="w-full h-full object-cover">
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-auto p-8">
            <?php if ($status === 'added'): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                Data dosen berhasil ditambahkan.
            </div>
            <?php elseif ($status === 'updated'): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                Data dosen berhasil diperbarui.
            </div>
            <?php elseif ($status === 'deleted'): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                Data dosen berhasil dihapus.
            </div>
            <?php elseif ($status === 'error'): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Proses gagal dijalankan.
            </div>
            <?php endif; ?>

            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <button class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium">
                        <i class="fa-regular fa-building"></i> Fakultas
                        <i class="fa-solid fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                    <button class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-filter text-gray-400"></i> Semua Dosen <i class="fa-solid fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.location.reload()" class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-rotate-right text-gray-400"></i> Refresh
                    </button>
                    <button type="button" onclick="openAddModal()" class="flex items-center gap-2 px-4 py-1.5 text-sm bg-black text-white rounded-md hover:bg-gray-800 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-plus"></i> Tambah Dosen
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Total Dosen</span>
                        <div class="w-8 h-8 rounded bg-blue-50 flex items-center justify-center text-blue-500">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Data pada tabel tbl_dosen</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold"><?php echo number_format($total_dosen); ?></span>
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
                    <div class="text-xs text-gray-400 mb-1">Tambah atau ubah data dosen</div>
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

            <div class="mb-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Daftar Dosen</h2>
                        <p class="text-xs text-gray-500">Kelola data dosen yang tersimpan di database</p>
                    </div>
                    <div class="flex gap-2 border border-gray-200 rounded-md overflow-hidden bg-white">
                        <button type="button" onclick="openExportModal({table: 'tbl_dosen', cols: 'nid,namados', filename: 'dosen_list'})" class="px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2 border-r border-gray-200">
                            <i class="fa-solid fa-file-export text-gray-400"></i> Export
                        </button>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white mb-6">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50/50">
                                <th class="font-medium text-gray-500 py-3 px-4 w-12 text-center">No</th>
                                <th class="font-medium text-gray-500 py-3 px-4">NID</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Nama Dosen</th>
                                <th class="font-medium text-gray-500 py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-center text-gray-500"><?php echo $no++; ?></td>
                                    <td class="py-3 px-4 text-gray-500 font-mono text-xs"><?php echo htmlspecialchars($row['nid']); ?></td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600 uppercase">
                                                <?php echo isset($row['namados'][0]) ? htmlspecialchars($row['namados'][0]) : '?'; ?>
                                            </div>
                                            <span class="font-medium text-gray-900"><?php echo htmlspecialchars($row['namados']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button" data-nid="<?php echo htmlspecialchars($row['nid'], ENT_QUOTES); ?>" data-namados="<?php echo htmlspecialchars($row['namados'], ENT_QUOTES); ?>" onclick="openEditModalFromButton(this)" class="px-3 py-1.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-100">
                                                Edit
                                            </button>
                                            <button type="button" data-nid="<?php echo htmlspecialchars($row['nid'], ENT_QUOTES); ?>" data-namados="<?php echo htmlspecialchars($row['namados'], ENT_QUOTES); ?>" onclick="openDeleteModalFromButton(this)" class="px-3 py-1.5 rounded-md text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 border border-red-100">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-400">
                                        <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-sm font-medium text-gray-500">Belum ada data dosen.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
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
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Dosen</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Buat data dosen baru tanpa pindah halaman.</p>
                </div>
                <button type="button" onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NID</label>
                    <input type="text" name="nid" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Dosen</label>
                    <input type="text" name="namados" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('addModal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" name="save_dosen" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Dosen</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data dosen di tempat yang sama.</p>
                </div>
                <button type="button" onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NID</label>
                    <input type="text" id="edit_nid" name="nid" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Dosen</label>
                    <input type="text" id="edit_namados" name="namados" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('editModal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" name="update_dosen" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Hapus Dosen</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Konfirmasi sebelum data dihapus.</p>
                </div>
                <button type="button" onclick="closeModal('deleteModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form method="POST" class="p-6 space-y-5">
                <input type="hidden" id="delete_nid" name="nid">
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    Apakah kamu yakin ingin menghapus data dosen <span id="delete_namados" class="font-semibold"></span>?
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('deleteModal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" name="delete_dosen" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const openModalId = <?php echo json_encode($open_modal); ?>;
        const editDosen = <?php echo json_encode($edit_dosen); ?>;
        const deleteDosen = <?php echo json_encode($delete_dosen); ?>;

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
            document.getElementById('edit_nid').value = button.dataset.nid || '';
            document.getElementById('edit_namados').value = button.dataset.namados || '';
            showModal('editModal');
        }

        function openDeleteModalFromButton(button) {
            document.getElementById('delete_nid').value = button.dataset.nid || '';
            document.getElementById('delete_namados').textContent = button.dataset.namados || '';
            showModal('deleteModal');
        }

        if (openModalId === 'add') {
            showModal('addModal');
        }

        if (openModalId === 'edit') {
            if (editDosen) {
                document.getElementById('edit_nid').value = editDosen.nid || '';
                document.getElementById('edit_namados').value = editDosen.namados || '';
            }
            showModal('editModal');
        }

        if (openModalId === 'delete') {
            if (deleteDosen) {
                document.getElementById('delete_nid').value = deleteDosen.nid || '';
                document.getElementById('delete_namados').textContent = deleteDosen.namados || '';
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
    </script>
    <?php require_once 'export-modal.php'; ?>
</body>
</html>