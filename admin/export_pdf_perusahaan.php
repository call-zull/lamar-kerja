<?php
session_start();
include '../includes/db.php';
require '../vendor/autoload.php'; // Path ke autoload.php Composer

use Mpdf\Mpdf;

$mpdf = new Mpdf();

// Fetch data
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'perusahaan'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '<h1>Daftar Perusahaan</h1>';
$html .= '<table border="1" cellpadding="10" cellspacing="0">';
$html .= '<thead><tr>
            <th>No</th>
            <th>Username</th>
            <th>No. WhatsApp</th>
            <th>Status</th>
          </tr></thead>';
$html .= '<tbody>';

$number = 1; // Variable untuk nomor urut
foreach ($rows as $row) {
    $status = ($row['approved'] == 1) ? 'Diterima' : (($row['approved'] == 2) ? 'Ditolak' : 'Pending');
    $html .= '<tr>';
    $html .= '<td>' . $number . '</td>';
    $html .= '<td>' . htmlspecialchars($row['username']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['no_telp']) . '</td>';
    $html .= '<td>' . $status . '</td>';
    $html .= '</tr>';
    $number++;
}

$html .= '</tbody></table>';

$mpdf->WriteHTML($html);
$mpdf->Output('data_perusahaan.pdf', 'I'); // Output to browser
?>
