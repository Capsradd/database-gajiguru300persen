<!-- Sidebar -->
    <aside class="w-64 border-r border-gray-200 flex flex-col h-full bg-white flex-shrink-0">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-gray-200 justify-between">
            <div class="flex items-center gap-2 font-bold text-xl">
                <img src="logo.png" alt="Logo" class="w-6 h-6">
                Unidb
            </div>
            <button class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-bars-staggered"></i></button>
        </div>

        <div class="p-4 overflow-y-auto flex-1 config-scrollbar">
            <!-- Search -->
            <div class="relative mb-6">
                <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text" placeholder="Search" class="w-full bg-gray-50 text-sm rounded-md pl-9 pr-3 py-2 focus:outline-none focus:ring-1 focus:ring-gray-300 border border-gray-200">
                <div class="absolute right-3 top-2.5 text-xs text-gray-400 border border-gray-200 rounded px-1">⌘K</div>
            </div>

            <!-- Menu -->
            <nav class="space-y-1 mb-8">
                <a href="index.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md">
                    <i class="fa-solid fa-house w-4 text-center"></i> Home
                </a>
                <a href="404.php?target=mahasiswa.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md">
                    <i class="fa-solid fa-user-graduate w-4 text-center"></i> Mahasiswa
                </a>
                <a href="dosen.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md">
                    <i class="fa-solid fa-chalkboard-user w-4 text-center"></i> Dosen
                </a>
                <a href="404.php?target=matkul.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md">
                    <i class="fa-solid fa-book-open w-4 text-center"></i> Mata Kuliah
                </a>
                <a href="404.php?target=nilai.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md">
                    <i class="fa-solid fa-star w-4 text-center"></i> Nilai
                </a>
                <a href="404.php?target=dopem.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md">
                    <i class="fa-solid fa-user-tie w-4 text-center"></i> Dopem
                </a>
                <a href="404.php?target=anggota.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md">
                    <i class="fa-solid fa-users w-4 text-center"></i> Anggota
                </a>
                <a href="console.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md">
                    <i class="fa-solid fa-terminal w-4 text-center"></i> SQL Console
                </a>
            </nav>
        </div>

        <!-- Bottom Sidebar -->
        <div class="p-4 border-t border-gray-200 mt-auto relative group">
            <!-- Dropdown Menu -->
            <div class="absolute bottom-full left-4 right-4 mb-2 bg-white border border-gray-200 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                <div class="p-1">
                    <a href="proses_logout.php" class="flex items-center gap-3 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md w-full text-left">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Profile (Dinamic dari session DB) -->
            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-md cursor-pointer border border-transparent hover:border-gray-200 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gray-900 text-white rounded flex items-center justify-center font-bold text-lg uppercase">
                        <?php echo isset($_SESSION['db_user']) ? substr($_SESSION['db_user'], 0, 1) : '?'; ?>
                    </div>
                    <div>
                        <div class="text-sm font-medium pr-2 truncate max-w[100px]"><?php echo isset($_SESSION['db_user']) ? htmlspecialchars($_SESSION['db_user']) : 'Not Connected'; ?></div>
                        <div class="text-xs text-gray-500">@localhost</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-up text-gray-400 text-xs"></i>
            </div>
        </div>
    </aside>
