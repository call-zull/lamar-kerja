<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id_pelamar']) || !isset($_POST['lowongan_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID pelamar atau lowongan ID tidak ditemukan.']);
        exit;
    }

    $id_pelamar = $_POST['id_pelamar'];
    $lowongan_id = $_POST['lowongan_id'];

    // Update status lamaran menjadi 'ditolak'
    $sql = "UPDATE lamaran_mahasiswas SET status = 'ditolak' WHERE mahasiswa_id = :id_pelamar AND lowongan_id = :lowongan_id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([':id_pelamar' => $id_pelamar, ':lowongan_id' => $lowongan_id]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Pelamar berhasil ditolak.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status lamaran.']);
    }
}
?>
