<!-- Reusable Export Modal Component -->
<div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Export Data</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pilih format dan nama file untuk export.</p>
            </div>
            <button type="button" onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="exportForm" method="GET" action="export.php" target="_blank" class="p-6 space-y-4">
            <input type="hidden" name="table" id="export_table">
            <input type="hidden" name="cols" id="export_cols">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Format</label>
                <select id="export_format" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" disabled>
                    <option value="csv">CSV (Excel compatible)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama File</label>
                <input type="text" name="filename" id="export_filename" placeholder="nama_file" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeExportModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit" class="rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Download</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Open export modal with params: {table, cols (comma list), filename (optional)}
    function openExportModal(params) {
        params = params || {};
        var table = params.table || '';
        var cols = params.cols || '';
        var filename = params.filename || (table ? table + '_' + new Date().toISOString().slice(0,19).replace(/[:T]/g,'_') : 'export');

        document.getElementById('export_table').value = table;
        document.getElementById('export_cols').value = cols;
        document.getElementById('export_filename').value = filename;

        var modal = document.getElementById('exportModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // close when clicking outside
        setTimeout(function(){
            modal.addEventListener('click', exportModalOutsideClick);
        }, 0);
    }

    function closeExportModal() {
        var modal = document.getElementById('exportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.removeEventListener('click', exportModalOutsideClick);
    }

    function exportModalOutsideClick(e) {
        if (e.target && e.target.id === 'exportModal') closeExportModal();
    }
</script>
