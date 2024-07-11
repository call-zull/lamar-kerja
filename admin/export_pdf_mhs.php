<?php
session_start();
include '../includes/db.php';
require '../vendor/autoload.php'; // Path ke autoload.php Composer

use Mpdf\Mpdf;

$mpdf = new Mpdf();

// Fetch data
$stmt = $pdo->query("SELECT m.*, u.username, u.no_telp, j.nama_jurusan, p.nama_prodi 
                     FROM mahasiswas m 
                     JOIN users u ON m.user_id = u.id 
                     JOIN jurusans j ON m.jurusan_id = j.id 
                     JOIN prodis p ON m.prodi_id = p.id");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '<h1>Daftar Mahasiswa</h1>';
$html .= '<table border="1" cellpadding="10" cellspacing="0">';
$html .= '<thead><tr>
            <th>No</th>
            <th>Username</th>
            <th>NIM</th>
            <th>Jurusan</th>
            <th>Prodi</th>
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
    $html .= '<td>' . htmlspecialchars($row['nim']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['nama_jurusan']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['nama_prodi']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['no_telp']) . '</td>';
    $html .= '<td>' . $status . '</td>';
    $html .= '</tr>';
    $number++;
}

$html .= '</tbody></table>';

$mpdf->WriteHTML($html);
$mpdf->Output('data_mahasiswa.pdf', 'I'); // Output to browser
?>
