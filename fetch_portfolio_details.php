<?php
require_once __DIR__ . '/includes/db.php';

$id = $_GET['id'];

$details = [];

// Fetch Mahasiswa Info
$sql = "SELECT m.nim FROM mahasiswas m WHERE m.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);
$details['mahasiswa'] = $mahasiswa;

// Fetch Sertifikasi
$sql = "SELECT s.nama_sertifikasi, s.nomor_sk, l.nama_lembaga AS lembaga, s.tanggal_diperoleh, s.tanggal_kadaluarsa, s.bukti 
        FROM sertifikasi s
        JOIN penyelenggara_sertifikasi l ON s.lembaga_id = l.id
        WHERE s.mahasiswa_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$details['sertifikasi'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Lomba
$sql = "SELECT l.nama_lomba, l.prestasi, k.nama_kategori AS kategori, t.nama AS tingkatan, l.penyelenggara, l.tanggal_pelaksanaan, l.tempat_pelaksanaan, l.bukti 
        FROM lomba l
        JOIN kategori k ON l.id_kategori = k.id
        JOIN tingkatan t ON l.id_tingkatan = t.id
        WHERE l.mahasiswa_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$details['lomba'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Pelatihan
$sql = "SELECT p.nama_pelatihan, p.materi, p.deskripsi, t.nama AS tingkatan, p.penyelenggara, p.tanggal_mulai, p.tanggal_selesai, p.tempat_pelaksanaan, p.bukti 
        FROM pelatihan p
        JOIN tingkatan t ON p.id_tingkatan = t.id
        WHERE p.mahasiswa_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$details['pelatihan'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Proyek
$sql = "SELECT p.nama_proyek, p.partner, p.peran, p.waktu_awal, p.waktu_selesai, p.tujuan_proyek, p.bukti 
        FROM proyek p
        WHERE p.mahasiswa_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$details['proyek'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($details);
?>
