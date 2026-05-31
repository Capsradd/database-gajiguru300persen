<?php
require_once 'koneksi.php';

// Require login
if (!$is_logged_in) {
    header('Location: index.php');
    exit;
}

$table = $_GET['table'] ?? '';
$colsParam = $_GET['cols'] ?? '';
$filename = $_GET['filename'] ?? '';

// Basic validation for table name
if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
    http_response_code(400);
    echo 'Invalid table name';
    exit;
}

// Sanitize and prepare column list if provided
if ($colsParam !== '') {
    $cols = array_map('trim', explode(',', $colsParam));
    foreach ($cols as $c) {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $c)) {
            http_response_code(400);
            echo 'Invalid column name';
            exit;
        }
    }
    $selectCols = implode(',', array_map(function($c){ return "`".$c."`"; }, $cols));
} else {
    $selectCols = '*';
}

if ($filename === '') {
    $filename = sprintf('export_%s_%s.csv', $table, date('Ymd_His'));
} else {
    // sanitize filename to avoid header injection
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
    if (substr(strtolower($filename), -4) !== '.csv') $filename .= '.csv';
}

$sql = "SELECT {$selectCols} FROM `{$table}`";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo 'Query error: ' . htmlspecialchars($conn->error);
    exit;
}

// Send CSV headers (Excel-friendly)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// UTF-8 BOM for Excel
echo "\xEF\xBB\xBF";
$out = fopen('php://output', 'w');

// Write header row
$fields = $result->fetch_fields();
$headers = [];
foreach ($fields as $f) $headers[] = $f->name;
fputcsv($out, $headers);

// Write data
$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
    $line = [];
    foreach ($headers as $h) $line[] = $row[$h];
    fputcsv($out, $line);
}

fclose($out);
exit;
