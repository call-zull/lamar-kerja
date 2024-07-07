<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $perusahaan_id = $_POST['perusahaan_id'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $email_perusahaan = $_POST['email_perusahaan'];
    $alamat_perusahaan = $_POST['alamat_perusahaan'];

    try {
        $sql = "UPDATE perusahaans SET nama_perusahaan = :nama_perusahaan, email_perusahaan = :email_perusahaan, alamat_perusahaan = :alamat_perusahaan WHERE id = :perusahaan_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama_perusahaan' => $nama_perusahaan,
            ':email_perusahaan' => $email_perusahaan,
            ':alamat_perusahaan' => $alamat_perusahaan,
            ':perusahaan_id' => $perusahaan_id,
        ]);

        $_SESSION['success_message'] = "Akun perusahaan berhasil diupdate.";
        header('Location: tampil_user_perusahaan.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: tampil_user_perusahaan.php');
    exit;
}
?>
