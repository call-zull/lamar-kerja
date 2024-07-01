<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['edit_id'];
    $nama_pekerjaan = $_POST['edit_nama_pekerjaan'];
    $posisi = $_POST['edit_posisi'];
    $kualifikasi = $_POST['edit_kualifikasi'];

    include '../config/database.php';

    $stmt = $conn->prepare("UPDATE lowongan SET nama_pekerjaan = ?, posisi = ?, kualifikasi = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nama_pekerjaan, $posisi, $kualifikasi, $id);

    if ($stmt->execute()) {
        header('Location: lowongan_kerja.php');
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
