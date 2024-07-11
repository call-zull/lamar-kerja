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
$sheet->setCellValue('C1', 'NIM');
$sheet->setCellValue('D1', 'Jurusan');
$sheet->setCellValue('E1', 'Prodi');
$sheet->setCellValue('F1', 'No. WhatsApp');
$sheet->setCellValue('G1', 'Status');

// Fetch data
$stmt = $pdo->query("SELECT m.*, u.username, u.no_telp, j.nama_jurusan, p.nama_prodi 
                     FROM mahasiswas m 
                     JOIN users u ON m.user_id = u.id 
                     JOIN jurusans j ON m.jurusan_id = j.id 
                     JOIN prodis p ON m.prodi_id = p.id");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rowNum = 2;
foreach ($rows as $row) {
    $sheet->setCellValue('A' . $rowNum, $rowNum - 1);
    $sheet->setCellValue('B' . $rowNum, $row['username']);
    $sheet->setCellValue('C' . $rowNum, $row['nim']);
    $sheet->setCellValue('D' . $rowNum, $row['nama_jurusan']);
    $sheet->setCellValue('E' . $rowNum, $row['nama_prodi']);
    $sheet->setCellValue('F' . $rowNum, $row['no_telp']);
    $sheet->setCellValue('G' . $rowNum, ($row['approved'] == 1) ? 'Diterima' : (($row['approved'] == 2) ? 'Ditolak' : 'Pending'));
    $rowNum++;
}

// Write to file
$writer = new Xlsx($spreadsheet);
$filename = 'data_mahasiswa.xlsx';
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
