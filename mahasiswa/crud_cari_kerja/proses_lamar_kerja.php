<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mahasiswa_id = $_POST['mahasiswa_id'];
    $lowongan_id = $_POST['lowongan_id'];
    $pesan = $_POST['pesan'];

    include '../../includes/db.php';
    try {
        $stmt = $pdo->prepare("INSERT INTO lamaran_mahasiswas (user_id, lowongan_id, pesan) VALUES (?, ?, ?)");
        $stmt->execute([$mahasiswa_id, $lowongan_id, $pesan]);

        // Set success message in session
        $_SESSION['success_message'] = "Lamaran berhasil dikirim!";
        header('Location: ../cari_kerja.php');
        exit;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
