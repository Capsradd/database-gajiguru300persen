<?php
require_once 'koneksi.php';

$status = $_GET['status'] ?? '';
$open_modal = $_GET['modal'] ?? '';
$query = null;
$query_error = '';
$total_nilai = 0;
$rata_akhir = null;
$nilai_tertinggi = null;
$nilai_terendah = null;
$table_exists = true;
$edit_nilai = null;
$delete_nilai = null;

if ($is_logged_in && $conn) {
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'tbl_nilai'");

    if ($table_check && mysqli_num_rows($table_check) > 0) {
        if (isset($_POST['save_nilai'])) {
            $nim = trim($_POST['nim'] ?? '');
            $tugas = (int) ($_POST['tugas'] ?? 0);
            $uts = (int) ($_POST['uts'] ?? 0);
            $uas = (int) ($_POST['uas'] ?? 0);
            $akhir = (int) ($_POST['akhir'] ?? 0);

            $stmt = $conn->prepare('INSERT INTO tbl_nilai (nim, tugas, uts, uas, akhir) VALUES (?, ?, ?, ?, ?)');

            if ($stmt) {
                $stmt->bind_param('siiii', $nim, $tugas, $uts, $uas, $akhir);

                if ($stmt->execute()) {
                    $stmt->close();
                    header('Location: nilai.php?status=added');
                    exit;
                }

                $query_error = $stmt->error;
                $stmt->close();
            } else {
                $query_error = mysqli_error($conn);
            }
        }

        if (isset($_POST['update_nilai'])) {
            $nim = trim($_POST['nim'] ?? '');
            $tugas = (int) ($_POST['tugas'] ?? 0);
            $uts = (int) ($_POST['uts'] ?? 0);
            $uas = (int) ($_POST['uas'] ?? 0);
            $akhir = (int) ($_POST['akhir'] ?? 0);

            $stmt = $conn->prepare('UPDATE tbl_nilai SET tugas = ?, uts = ?, uas = ?, akhir = ? WHERE nim = ?');

            if ($stmt) {
                $stmt->bind_param('iiiis', $tugas, $uts, $uas, $akhir, $nim);

                if ($stmt->execute()) {
                    $stmt->close();
                    header('Location: nilai.php?status=updated');
                    exit;
                }

                $query_error = $stmt->error;
                $stmt->close();
            } else {
                $query_error = mysqli_error($conn);
            }
        }

        if (isset($_POST['delete_nilai'])) {
            $nim = trim($_POST['nim'] ?? '');

            $stmt = $conn->prepare('DELETE FROM tbl_nilai WHERE nim = ?');

            if ($stmt) {
                $stmt->bind_param('s', $nim);

                if ($stmt->execute()) {
                    $stmt->close();
                    header('Location: nilai.php?status=deleted');
                    exit;
                }

                $query_error = $stmt->error;
                $stmt->close();
            } else {
                $query_error = mysqli_error($conn);
            }
        }

        $edit_nim = trim($_GET['edit'] ?? '');
        if ($edit_nim !== '') {
            $stmt = $conn->prepare('SELECT nim, tugas, uts, uas, akhir FROM tbl_nilai WHERE nim = ? LIMIT 1');
            if ($stmt) {
                $stmt->bind_param('s', $edit_nim);
                $stmt->execute();
                $result = $stmt->get_result();
                $edit_nilai = $result ? $result->fetch_assoc() : null;
                $stmt->close();
                $open_modal = 'edit';
            }
        }

        $delete_nim = trim($_GET['delete'] ?? '');
        if ($delete_nim !== '') {
            $stmt = $conn->prepare('SELECT nim, tugas, uts, uas, akhir FROM tbl_nilai WHERE nim = ? LIMIT 1');
            if ($stmt) {
                $stmt->bind_param('s', $delete_nim);
                $stmt->execute();
                $result = $stmt->get_result();
                $delete_nilai = $result ? $result->fetch_assoc() : null;
                $stmt->close();
                $open_modal = 'delete';
            }
        }

        $query = mysqli_query($conn, 'SELECT nim, tugas, uts, uas, akhir FROM tbl_nilai ORDER BY nim ASC');

        if ($query) {
            $total_nilai = mysqli_num_rows($query);

            $summary = mysqli_query($conn, 'SELECT AVG(akhir) AS rata_akhir, MAX(akhir) AS nilai_tertinggi, MIN(akhir) AS nilai_terendah FROM tbl_nilai');

            if ($summary) {
                $summary_row = mysqli_fetch_assoc($summary);
                $rata_akhir = $summary_row['rata_akhir'] ?? null;
                $nilai_tertinggi = $summary_row['nilai_tertinggi'] ?? null;
                $nilai_terendah = $summary_row['nilai_terendah'] ?? null;
            }
        } else {
            $query_error = mysqli_error($conn);
        }
    } else {
        $table_exists = false;

        if (!$table_check) {
            $query_error = mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Nilai - Unidb</title>
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
                <h1 class="text-lg font-semibold text-gray-900">Data Nilai</h1>
                <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                    <i class="fa-regular fa-clock"></i> Terakhir diperbarui: Baru saja
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Search Nilai" class="bg-gray-50 text-sm rounded-md pl-9 pr-4 py-2 w-64 focus:outline-none border border-gray-200">
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
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">Data nilai berhasil ditambahkan.</div>
            <?php elseif ($status === 'updated'): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">Data nilai berhasil diperbarui.</div>
            <?php elseif ($status === 'deleted'): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">Data nilai berhasil dihapus.</div>
            <?php elseif ($status === 'error'): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">Proses gagal dijalankan.</div>
            <?php endif; ?>

            <?php if ($is_logged_in && !$table_exists): ?>
            <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <p class="text-sm font-semibold">Tabel tbl_nilai belum ditemukan.</p>
                        <p class="text-sm mt-1">Buat tabel dulu sebelum memakai halaman nilai.</p>
                    </div>
                </div>
                <div class="mt-3 rounded-md border border-amber-200 bg-white p-3">
                    <p class="text-xs font-semibold text-amber-700 mb-2">SQL yang bisa dijalankan:</p>
                    <pre class="text-xs leading-5 overflow-auto text-gray-700">CREATE TABLE tbl_nilai (
    nim CHAR(10) PRIMARY KEY,
    tugas INT NOT NULL,
    uts INT NOT NULL,
    uas INT NOT NULL,
    akhir INT NOT NULL
);</pre>
                </div>
            </div>
            <?php elseif ($query_error !== ''): ?>
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Gagal membaca data nilai: <?php echo htmlspecialchars($query_error); ?>
            </div>
            <?php endif; ?>

            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <button class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium">
                        <i class="fa-solid fa-graduation-cap"></i> Nilai Mahasiswa
                        <i class="fa-solid fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                    <button class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-filter text-gray-400"></i> Semua Nilai <i class="fa-solid fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.location.reload()" class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-rotate-right text-gray-400"></i> Refresh
                    </button>
                    <button type="button" onclick="openAddModal()" class="flex items-center gap-2 px-4 py-1.5 text-sm bg-black text-white rounded-md hover:bg-gray-800 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-plus"></i> Tambah Nilai
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Total Mahasiswa</span>
                        <div class="w-8 h-8 rounded bg-blue-50 flex items-center justify-center text-blue-500"><i class="fa-solid fa-list-check"></i></div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Data pada tabel tbl_nilai</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold"><?php echo number_format($total_nilai); ?></span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">Aktif</span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Rata-rata Akhir</span>
                        <div class="w-8 h-8 rounded bg-green-50 flex items-center justify-center text-green-500"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Nilai keseluruhan</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold"><?php echo $rata_akhir !== null ? number_format((float) $rata_akhir, 2) : '-'; ?></span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-green-100 text-green-700">Summary</span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Nilai Tertinggi</span>
                        <div class="w-8 h-8 rounded bg-orange-50 flex items-center justify-center text-orange-500"><i class="fa-solid fa-arrow-up-wide-short"></i></div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Kolom akhir</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold"><?php echo $nilai_tertinggi !== null ? htmlspecialchars((string) $nilai_tertinggi) : '-'; ?></span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-orange-100 text-orange-700">Max</span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Nilai Terendah</span>
                        <div class="w-8 h-8 rounded bg-red-50 flex items-center justify-center text-red-500"><i class="fa-solid fa-arrow-down-wide-short"></i></div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Kolom akhir</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold"><?php echo $nilai_terendah !== null ? htmlspecialchars((string) $nilai_terendah) : '-'; ?></span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-red-100 text-red-700">Min</span>
                    </div>
                </div>
            </div>
            <div class="flex-1 flex flex-col min-h-0">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Daftar Nilai</h2>
                        <p class="text-xs text-gray-500">Data Nilai Mahasiswa dari database</p>
                    </div>
                    <div class="flex gap-2 border border-gray-200 rounded-md overflow-hidden bg-white">
                        <button type="button" onclick="openExportModal({table: 'tbl_nilai', cols: 'nim,tugas,uts,uas,akhir', filename: 'nilai_list'})" class="px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2 border-r border-gray-200">
                            <i class="fa-solid fa-file-export text-gray-400"></i> Export
                        </button>
                    </div>
                </div>
            <div class="flex-1 min-h-0 border border-gray-200 rounded-lg overflow-auto bg-white">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/50">
                            <th class="font-medium text-gray-500 py-3 px-4 w-12 text-center">No</th>
                            <th class="font-medium text-gray-500 py-3 px-4">NIM</th>
                            <th class="font-medium text-gray-500 py-3 px-4">Tugas</th>
                            <th class="font-medium text-gray-500 py-3 px-4">UTS</th>
                            <th class="font-medium text-gray-500 py-3 px-4">UAS</th>
                            <th class="font-medium text-gray-500 py-3 px-4">Akhir</th>
                            <th class="font-medium text-gray-500 py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if ($query && mysqli_num_rows($query) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-center text-gray-500"><?php echo $no++; ?></td>
                                <td class="py-3 px-4 text-gray-700 font-mono text-xs"><?php echo htmlspecialchars($row['nim']); ?></td>
                                <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars((string) $row['tugas']); ?></td>
                                <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars((string) $row['uts']); ?></td>
                                <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars((string) $row['uas']); ?></td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium text-green-700 bg-green-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> <?php echo htmlspecialchars((string) $row['akhir']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" data-nim="<?php echo htmlspecialchars($row['nim'], ENT_QUOTES); ?>" data-tugas="<?php echo htmlspecialchars((string) $row['tugas'], ENT_QUOTES); ?>" data-uts="<?php echo htmlspecialchars((string) $row['uts'], ENT_QUOTES); ?>" data-uas="<?php echo htmlspecialchars((string) $row['uas'], ENT_QUOTES); ?>" data-akhir="<?php echo htmlspecialchars((string) $row['akhir'], ENT_QUOTES); ?>" onclick="openEditModalFromButton(this)" class="px-3 py-1.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-100">Edit</button>
                                        <button type="button" data-nim="<?php echo htmlspecialchars($row['nim'], ENT_QUOTES); ?>" data-akhir="<?php echo htmlspecialchars((string) $row['akhir'], ENT_QUOTES); ?>" onclick="openDeleteModalFromButton(this)" class="px-3 py-1.5 rounded-md text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 border border-red-100">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                        <td colspan="7" class="py-12 text-center text-gray-400">
                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p class="text-sm font-medium text-gray-500">Belum ada data nilai.</p>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr id="noResultRow" class="hidden">
                    <td colspan="7" class="py-12 text-center text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm font-medium text-gray-500">
                            Data nilai tidak ditemukan.
                        </p>
                    </td>
                   </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="addModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Nilai</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Buat data nilai baru tanpa pindah halaman.</p>
                </div>
                <button type="button" onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NIM</label>
                    <input type="text" name="nim" required maxlength="10" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tugas</label>
                        <input type="number" name="tugas" required min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">UTS</label>
                        <input type="number" name="uts" required min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">UAS</label>
                        <input type="number" name="uas" required min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Akhir</label>
                        <input type="number" name="akhir" required min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('addModal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" name="save_nilai" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Nilai</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data nilai di tempat yang sama.</p>
                </div>
                <button type="button" onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NIM</label>
                    <input type="text" id="edit_nim" name="nim" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tugas</label>
                        <input type="number" id="edit_tugas" name="tugas" required min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">UTS</label>
                        <input type="number" id="edit_uts" name="uts" required min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">UAS</label>
                        <input type="number" id="edit_uas" name="uas" required min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Akhir</label>
                        <input type="number" id="edit_akhir" name="akhir" required min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-black focus:outline-none focus:ring-2 focus:ring-black/10">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('editModal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" name="update_nilai" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Hapus Nilai</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Konfirmasi sebelum data dihapus.</p>
                </div>
                <button type="button" onclick="closeModal('deleteModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form method="POST" class="p-6 space-y-5">
                <input type="hidden" id="delete_nim" name="nim">
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    Apakah kamu yakin ingin menghapus data nilai dengan NIM <span id="delete_nim_label" class="font-semibold"></span>?
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('deleteModal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" name="delete_nilai" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const openModalId = <?php echo json_encode($open_modal); ?>;
        const editNilai = <?php echo json_encode($edit_nilai); ?>;
        const deleteNilai = <?php echo json_encode($delete_nilai); ?>;

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
            document.getElementById('edit_nim').value = button.dataset.nim || '';
            document.getElementById('edit_tugas').value = button.dataset.tugas || '';
            document.getElementById('edit_uts').value = button.dataset.uts || '';
            document.getElementById('edit_uas').value = button.dataset.uas || '';
            document.getElementById('edit_akhir').value = button.dataset.akhir || '';
            showModal('editModal');
        }

        function openDeleteModalFromButton(button) {
            document.getElementById('delete_nim').value = button.dataset.nim || '';
            document.getElementById('delete_nim_label').textContent = button.dataset.nim || '';
            showModal('deleteModal');
        }

        if (openModalId === 'add') {
            showModal('addModal');
        }

        if (openModalId === 'edit') {
            if (editNilai) {
                document.getElementById('edit_nim').value = editNilai.nim || '';
                document.getElementById('edit_tugas').value = editNilai.tugas || '';
                document.getElementById('edit_uts').value = editNilai.uts || '';
                document.getElementById('edit_uas').value = editNilai.uas || '';
                document.getElementById('edit_akhir').value = editNilai.akhir || '';
            }
            showModal('editModal');
        }

        if (openModalId === 'delete') {
            if (deleteNilai) {
                document.getElementById('delete_nim').value = deleteNilai.nim || '';
                document.getElementById('delete_nim_label').textContent = deleteNilai.nim || '';
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
        const searchInput = document.getElementById('searchInput');
        const noResultRow = document.getElementById('noResultRow');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();

                const rows = document.querySelectorAll('tbody tr:not(#noResultRow)');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();

                    if (text.includes(keyword)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (noResultRow) {
                    noResultRow.classList.toggle('hidden', visibleCount > 0);
                }
            });
        }
    </script>

</body>
</html>