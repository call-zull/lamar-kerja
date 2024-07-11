<?php
session_start();
include '../includes/db.php';
require '../vendor/autoload.php'; // Pastikan path ke autoload.php benar

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Username');
$sheet->setCellValue('C1', 'No. WhatsApp');
$sheet->setCellValue('D1', 'Status');

// Fetch data
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'perusahaan'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rowNum = 2;
foreach ($rows as $row) {
    $status = ($row['approved'] == 1) ? 'Diterima' : (($row['approved'] == 2) ? 'Ditolak' : 'Pending');
    $sheet->setCellValue('A' . $rowNum, $rowNum - 1);
    $sheet->setCellValue('B' . $rowNum, $row['username']);
    $sheet->setCellValue('C' . $rowNum, $row['no_telp']);
    $sheet->setCellValue('D' . $rowNum, $status);
    $rowNum++;
}

// Write to file
$writer = new Xlsx($spreadsheet);
$filename = 'data_perusahaan.xlsx';
$writer->save($filename);

// Set headers to prompt download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Cache-Control: max-age=0');
readfile($filename);

// Remove file after download
unlink($filename);
exit;
?>
