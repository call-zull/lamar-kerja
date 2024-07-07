<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cdc_id = $_POST['cdc_id'];
    $nama_cdc = $_POST['nama_cdc'];
    $alamat_cdc = $_POST['alamat_cdc'];
    $email_cdc = $_POST['email_cdc'];

    try {
        $sql = "UPDATE cdcs SET nama_cdc = :nama_cdc, alamat_cdc = :alamat_cdc, email_cdc = :email_cdc WHERE id = :cdc_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama_cdc' => $nama_cdc,
            ':alamat_cdc' => $alamat_cdc,
            ':email_cdc' => $email_cdc,
            ':cdc_id' => $cdc_id,
        ]);

        $_SESSION['success_message'] = "Akun CDC berhasil diupdate.";
        header('Location: tampil_user_cdc.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: tampil_user_cdc.php');
    exit;
}
?>
