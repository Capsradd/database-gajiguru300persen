<?php
require_once 'koneksi.php';
require_once 'sidebar.php';

// Proteksi jika belum login
if (!$is_logged_in) {
    header("Location: index.php");
    exit;
}

$query_result = null;
$query_error = '';
$executed_query = '';

// Jika tombol 'Run SQL' diklik
if (isset($_POST['execute_sql'])) {
    $executed_query = trim($_POST['sql_query']);
    
    if (!empty($executed_query)) {
        // Melakukan eksekusi query dengan blok try-catch agar tidak crash di PHP 8.1+
        try {
            $result = $conn->query($executed_query);
            
            if ($result === false) {
                // Fallback untuk PHP versi lama
                $query_error = $conn->error;
            } else {
                // Jika hasil dari query mereturn recordset (seperti SELECT, SHOW, dll)
                if ($result instanceof mysqli_result) {
                    // Ambil header kolom
                    $fields = [];
                    while ($field = $result->fetch_field()) {
                        $fields[] = $field->name;
                    }
                    
                    // Ambil data baris
                    $rows = [];
                    while ($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    
                    $query_result = [
                        'type' => 'select',
                        'fields' => $fields,
                        'rows' => $rows,
                        'num_rows' => $result->num_rows
                    ];
                } else {
                    // Return dari operasi INSERT, UPDATE, DELETE, CREATE, DROP (return boolean true)
                    $query_result = [
                        'type' => 'modify',
                        'affected_rows' => $conn->affected_rows
                    ];
                }
            }
        } catch (mysqli_sql_exception $e) {
            // Tangkap error query SQL
            $query_error = $e->getMessage();
        } catch (Exception $e) {
            // Tangkap error general lainnya
            $query_error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Console - Unidb</title>
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
    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full bg-gray-50 overflow-hidden">
        
        <!-- Header -->
        <header class="h-16 bg-white flex items-center justify-between px-8 border-b border-gray-200 flex-shrink-0">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Database Console</h1>
                <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-database text-blue-500"></i> Connected to: <?php echo htmlspecialchars($db_name); ?>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col p-8 overflow-hidden">
            
            <div class="w-full flex-1 flex flex-col min-h-0">
                <!-- Editor -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm flex flex-col overflow-hidden">
                    <div class="bg-gray-800 text-gray-300 text-xs px-4 py-2 border-b border-gray-700 flex justify-between items-center shrink-0">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-code"></i> SQL Editor
                        </div>
                    </div>
                    <form method="POST" action="" class="flex flex-col flex-1 shrink-0 p-0 m-0 relative">
                        <textarea 
                            name="sql_query" 
                            class="w-full bg-[#1e1e1e] text-green-400 font-mono text-sm p-4 focus:outline-none resize-y min-h-[150px]" 
                            placeholder="SELECT * FROM table_name;"
                            spellcheck="false"
                        ><?php echo htmlspecialchars($executed_query); ?></textarea>
                        
                        <div class="bg-white px-4 py-3 border-t border-gray-200 flex justify-between items-center shrink-0">
                            <span class="text-xs text-gray-500"><i class="fa-solid fa-circle-info"></i> Gunakan sintaks MariaDB/MySQL.</span>
                            <button type="submit" name="execute_sql" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium text-sm flex items-center gap-2 transition-colors">
                                <i class="fa-solid fa-play"></i> Run SQL
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Result Area -->
                <div class="mt-6 flex-1 flex flex-col bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden min-h-0">
                    <div class="bg-gray-50 text-gray-700 text-xs font-semibold px-4 py-3 border-b border-gray-200 shrink-0 flex items-center gap-2">
                        <i class="fa-solid fa-table-list"></i> Query Result
                    </div>
                    
                    <div class="flex-1 overflow-auto bg-white">
                        <?php if ($query_error): ?>
                            <div class="p-6">
                                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg flex items-start gap-3">
                                    <i class="fa-solid fa-circle-xmark mt-0.5"></i>
                                    <div>
                                        <h4 class="font-semibold text-sm">Error executing query:</h4>
                                        <p class="text-sm mt-1 font-mono"><?php echo htmlspecialchars($query_error); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($query_result !== null): ?>
                            
                            <?php if ($query_result['type'] === 'modify'): ?>
                                <div class="p-6">
                                    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg flex items-center gap-3">
                                        <i class="fa-solid fa-circle-check text-lg"></i>
                                        <span class="font-medium">Query executed successfully. <?php echo $query_result['affected_rows']; ?> row(s) affected.</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Tabular Data -->
                                <?php if (count($query_result['rows']) > 0): ?>
                                    <div class="p-4 pb-2 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10">
                                        <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-1 rounded">
                                            Return: <?php echo $query_result['num_rows']; ?> rows
                                        </span>
                                    </div>
                                    <table class="w-full text-left border-collapse text-sm">
                                        <thead>
                                            <tr class="bg-gray-50/80 sticky top-[45px] z-10 shadow-sm">
                                                <th class="font-medium text-gray-500 py-2.5 px-4 border-b border-gray-200 border-r border-gray-100 w-12 text-center bg-gray-100">#</th>
                                                <?php foreach ($query_result['fields'] as $field): ?>
                                                    <th class="font-medium text-gray-700 py-2.5 px-4 border-b border-gray-200 border-r border-gray-100 whitespace-nowrap bg-gray-50">
                                                        <?php echo htmlspecialchars($field); ?>
                                                    </th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <?php foreach ($query_result['rows'] as $index => $row): ?>
                                                <tr class="hover:bg-blue-50/50 transition-colors group">
                                                    <td class="py-2 px-4 border-r border-gray-100 text-center text-gray-400 bg-gray-50 group-hover:bg-blue-50/50 text-xs">
                                                        <?php echo $index + 1; ?>
                                                    </td>
                                                    <?php foreach ($query_result['fields'] as $field): ?>
                                                        <td class="py-2 px-4 border-r border-gray-100 text-gray-600 truncate max-w-xs hover:max-w-none hover:whitespace-normal break-words">
                                                            <?php 
                                                                if ($row[$field] === null) echo '<em class="text-gray-400 font-serif">NULL</em>';
                                                                else echo htmlspecialchars($row[$field]); 
                                                            ?>
                                                        </td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="p-12 flex flex-col justify-center items-center text-gray-400">
                                        <i class="fa-solid fa-box-open text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-sm font-medium text-gray-500">Query returned an empty result set (0 rows).</p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="p-12 flex flex-col justify-center items-center text-gray-400 h-full">
                                <i class="fa-solid fa-terminal text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm font-medium text-gray-500">Run a query to see the results here.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
        </div>
    </main>
</body>
</html>