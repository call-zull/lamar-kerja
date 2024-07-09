<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mahasiswa_id = $_POST['mahasiswa_id']; // Using the ID passed from the form
    $lowongan_id = $_POST['lowongan_id'];
    $pesan = $_POST['pesan'];

    include '../../includes/db.php';
    try {
        // Periksa apakah mahasiswa_id ada dalam tabel mahasiswas
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswas WHERE id = ?");
        $check_stmt->execute([$mahasiswa_id]);
        $mahasiswa_exists = $check_stmt->fetchColumn();

        if ($mahasiswa_exists) {
            // Masukkan lamaran ke dalam tabel lamaran_mahasiswas
            $insert_stmt = $pdo->prepare("INSERT INTO lamaran_mahasiswas (mahasiswa_id, lowongan_id, pesan, status) VALUES (?, ?, ?, 'Pending')");
            $insert_stmt->execute([$mahasiswa_id, $lowongan_id, $pesan]);

            $_SESSION['success_message'] = "Lamaran berhasil dikirim!";
            header('Location: ../cari_kerja.php');
            exit;
        } else {
            // Handle jika mahasiswa_id tidak valid
            $_SESSION['error_message'] = "Mahasiswa tidak valid atau tidak ditemukan.";
            header('Location: ../cari_kerja.php');
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
