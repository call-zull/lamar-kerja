<?php
session_start();
include '../includes/db.php';
require 'vendor/autoload.php'; // Pastikan Anda telah menginstal PhpSpreadsheet melalui Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();