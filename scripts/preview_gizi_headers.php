<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
$path = __DIR__ . '/../dokumen/data_gizi.xlsx';
if (!file_exists($path)) {
    echo "File not found: $path\n";
    exit(1);
}
$reader = IOFactory::createReaderForFile($path);
$spreadsheet = $reader->load($path);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);
$header = array_shift($rows);
print_r($header);
