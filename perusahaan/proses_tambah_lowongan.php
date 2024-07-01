<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pekerjaan = $_POST['nama_pekerjaan'];
    $posisi = $_POST['posisi'];
    $kualifikasi = $_POST['kualifikasi'];

    include '../config/database.php';

    $stmt = $conn->prepare("INSERT INTO lowongan (nama_pekerjaan, posisi, kualifikasi) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama_pekerjaan, $posisi, $kualifikasi);

    if ($stmt->execute()) {
        header('Location: lowker.php');
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
