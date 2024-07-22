<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek apakah data yang diperlukan ada dalam request
    if (!isset($_POST['id_pelamar']) || !isset($_POST['salary']) || !isset($_POST['lowongan_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID pelamar, lowongan ID atau salary tidak ditemukan.']);
        exit;
    }

    $id_pelamar = $_POST['id_pelamar'];
    $salary = $_POST['salary'];
    $lowongan_id = $_POST['lowongan_id'];

    // Update status lamaran menjadi 'diterima' dan masukkan salary
    $sql = "UPDATE lamaran_mahasiswas SET status = 'diterima', salary = :salary WHERE mahasiswa_id = :id_pelamar AND lowongan_id = :lowongan_id";
    $stmt = $pdo->prepare($sql);

    try {
        $result = $stmt->execute([':id_pelamar' => $id_pelamar, ':salary' => $salary, ':lowongan_id' => $lowongan_id]);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Pelamar berhasil diterima.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status lamaran.']);
        }
    } catch (PDOException $e) {
        // Tampilkan pesan kesalahan jika terjadi exception
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}
?>
