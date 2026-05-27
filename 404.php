<?php
require_once 'koneksi.php';
require_once 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Database - Pathenol</title>
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

        <!-- Top Navigation Area -->
        <header class="h-16 border-b border-gray-200 flex items-center justify-between px-8 shrink-0">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <i class="fa-solid fa-house"></i>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-medium">Page Not Found</span>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-auto p-8 flex items-center justify-center">
            
            <div class="text-center">
                <div class="text-8xl font-black text-gray-200 mb-4">404</div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Halaman Belum Tersedia</h1>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">
                    Maaf, halaman 
                    <strong class="text-gray-800 bg-gray-100 px-2 py-1 rounded">
                        <?php echo isset($_GET['target']) ? htmlspecialchars($_GET['target']) : 'yang Anda tuju'; ?>
                    </strong>
                    sedang dalam tahap pengembangan atau tidak ditemukan.
                </p>
                <a href="index.php" class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors font-medium text-sm">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>

        </div>
    </main>
</body>
</html>
