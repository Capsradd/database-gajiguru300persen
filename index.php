<?php
require_once 'koneksi.php';
require_once 'sidebar.php';

$query = null;
$total_dosen = 0;
$rata_akhir = null;

if ($is_logged_in && $conn) {
    $query = mysqli_query($conn, "SELECT * FROM tbl_dosen ORDER BY nid ASC");

    if ($query) {
        $total_dosen = mysqli_num_rows($query);
    }

    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'tbl_nilai'");
    if ($table_check && mysqli_num_rows($table_check) > 0) {
        $summary = mysqli_query($conn, 'SELECT AVG(akhir) AS rata_akhir FROM tbl_nilai');
        if ($summary) {
            $summary_row = mysqli_fetch_assoc($summary);
            $rata_akhir = $summary_row['rata_akhir'] ?? null;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidb</title>
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
    <!-- Login Popup Overlay -->
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

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full bg-white overflow-hidden">
        
        <!-- Header -->
        <header class="h-16 flex items-center justify-between px-8 border-b border-gray-200 flex-shrink-0">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Sistem Informasi Kampus</h1>
                <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                    <i class="fa-regular fa-clock"></i> Terakhir diperbarui: Baru saja
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Search Database" class="bg-gray-50 text-sm rounded-md pl-9 pr-4 py-2 w-64 focus:outline-none border border-gray-200">
                </div>
                <button class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 border border-transparent"><i class="fa-regular fa-circle-question"></i></button>
                <button class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 border border-transparent"><i class="fa-regular fa-bell"></i></button>
                <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden border border-gray-300 ml-2 cursor-pointer">
                    <img src="https://ui-avatars.com/api/?name=User&background=random" alt="Profile" class="w-full h-full object-cover">
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-auto p-8">
            
            <!-- Toolbar -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <button class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium">
                        <i class="fa-regular fa-calendar"></i> Semester Ganjil 2026/2027 <i class="fa-solid fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                    <button class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-filter text-gray-400"></i> Program Studi Ilmu Komputer <i class="fa-solid fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.location.reload()" class="flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 text-gray-700 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-rotate-right text-gray-400"></i> Refresh
                    </button>
                    <button class="flex items-center gap-2 px-4 py-1.5 text-sm bg-black text-white rounded-md hover:bg-gray-800 font-medium whitespace-nowrap">
                        <i class="fa-solid fa-plus"></i> Tambah Mahasiswa
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-4 gap-4 mb-8">
                <!-- Stat Card 1 -->
                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Total Mahasiswa</span>
                        <div class="w-8 h-8 rounded bg-blue-50 flex items-center justify-center text-blue-500">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Mahasiswa Aktif</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold">1,245</span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-green-100 text-green-700">+12% smt ini</span>
                    </div>
                </div>
                <!-- Stat Card 2 -->
                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Total Dosen</span>
                        <div class="w-8 h-8 rounded bg-purple-50 flex items-center justify-center text-purple-500">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Dosen Tetap & LB</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold"><?php echo $total_dosen; ?></span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">Tetap</span>
                    </div>
                </div>
                <!-- Stat Card 3 -->
                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Mata Kuliah</span>
                        <div class="w-8 h-8 rounded bg-orange-50 flex items-center justify-center text-orange-500">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Kurikulum Aktif</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold">124</span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">Semua Prodi</span>
                    </div>
                </div>
                <!-- Stat Card 4 -->
                <div class="border border-gray-200 rounded-lg p-5 bg-white">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-sm font-medium text-gray-600">Rata-rata Nilai Akhir</span>
                        <div class="w-8 h-8 rounded bg-green-50 flex items-center justify-center text-green-500"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                    <div class="text-xs text-gray-400 mb-1">Nilai keseluruhan</div>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl font-bold"><?php echo $rata_akhir !== null ? number_format((float) $rata_akhir, 2) : '-'; ?></span>
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-green-100 text-green-700">Summary</span>
                    </div>
                </div>
            </div>

            <!-- Table Section (Mahasiswa Baru) -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Mahasiswa Baru (Pendaftar Terakhir)</h2>
                        <p class="text-xs text-gray-500">Data 5 mahasiswa terakhir yang masuk ke dalam sistem</p>
                    </div>
                    <div class="flex gap-2 border border-gray-200 rounded-md overflow-hidden bg-white">
                        <button class="px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2 border-r border-gray-200">
                            <i class="fa-solid fa-file-export text-gray-400"></i> Export
                        </button>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white mb-6">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50/50">
                                <th class="font-medium text-gray-500 py-3 px-4 w-12 text-center">No</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Nama Mahasiswa</th>
                                <th class="font-medium text-gray-500 py-3 px-4">NIM</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Program Studi</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Tanggal Daftar</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Status</th>
                                <th class="py-3 px-4 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- Row 1 -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-center text-gray-500">1</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=random" class="w-6 h-6 rounded-full" alt="Budi">
                                        <span class="font-medium text-gray-900">Budi Santoso</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-500 font-mono text-xs">20261001</td>
                                <td class="py-3 px-4 text-gray-600">Teknik Informatika</td>
                                <td class="py-3 px-4 text-gray-600">25 Mei 2026</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium text-green-700 bg-green-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-chevron-right"></i></button>
                                </td>
                            </tr>
                            <!-- Row 2 -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-center text-gray-500">2</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=Siti+Aminah&background=random" class="w-6 h-6 rounded-full" alt="Siti">
                                        <span class="font-medium text-gray-900">Siti Aminah</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-500 font-mono text-xs">20261002</td>
                                <td class="py-3 px-4 text-gray-600">Sistem Informasi</td>
                                <td class="py-3 px-4 text-gray-600">25 Mei 2026</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium text-green-700 bg-green-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-chevron-right"></i></button>
                                </td>
                            </tr>
                            <!-- Row 3 -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-center text-gray-500">3</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=Agus+Ridwan&background=random" class="w-6 h-6 rounded-full" alt="Agus">
                                        <span class="font-medium text-gray-900">Agus Ridwan</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-500 font-mono text-xs">20262001</td>
                                <td class="py-3 px-4 text-gray-600">Manajemen Bisnis</td>
                                <td class="py-3 px-4 text-gray-600">22 Mei 2026</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium text-yellow-700 bg-yellow-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Verifikasi
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-chevron-right"></i></button>
                                </td>
                            </tr>
                            <!-- Row 4 -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-center text-gray-500">4</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=Diana+Putri&background=random" class="w-6 h-6 rounded-full" alt="Diana">
                                        <span class="font-medium text-gray-900">Diana Putri</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-500 font-mono text-xs">20261003</td>
                                <td class="py-3 px-4 text-gray-600">Teknik Informatika</td>
                                <td class="py-3 px-4 text-gray-600">20 Mei 2026</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium text-green-700 bg-green-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-chevron-right"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table Section (Jadwal Dosen) -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Jadwal Kuliah Hari Ini</h2>
                        <p class="text-xs text-gray-500">Daftar kelas yang berlangsung hari ini</p>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50/50">
                                <th class="font-medium text-gray-500 py-3 px-4 w-12 text-center">No</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Batas Waktu</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Mata Kuliah</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Ruangan</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Dosen Pengampu</th>
                                <th class="font-medium text-gray-500 py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- Row 1 -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-center text-gray-500">1</td>
                                <td class="py-3 px-4 text-gray-600 font-medium">08:00 - 10:30</td>
                                <td class="py-3 px-4 text-gray-600">Pemrograman Web Lanjut</td>
                                <td class="py-3 px-4 text-gray-500 font-mono text-xs">LAB-RPLA</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=Eka+Pramana&background=random" class="w-6 h-6 rounded-full" alt="Eka">
                                        <span class="font-medium text-gray-900">Dr. Eka Pramana, M.Kom</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium text-green-700 bg-green-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Sedang Berlangsung
                                    </span>
                                </td>
                            </tr>
                             <!-- Row 2 -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-center text-gray-500">2</td>
                                <td class="py-3 px-4 text-gray-600 font-medium">13:00 - 15:30</td>
                                <td class="py-3 px-4 text-gray-600">Basis Data Terdistribusi</td>
                                <td class="py-3 px-4 text-gray-500 font-mono text-xs">RK-402</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=Rini+Wati&background=random" class="w-6 h-6 rounded-full" alt="Rini">
                                        <span class="font-medium text-gray-900">Rini Wati, S.T., M.T.</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium text-gray-700 bg-gray-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Menunggu
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
