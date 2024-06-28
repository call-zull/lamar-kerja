<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id = $_POST['id'];
    $nama_cdc = $_POST['nama_cdc'];
    $alamat_cdc = $_POST['alamat_cdc'];
    $email_cdc = $_POST['email_cdc'];

    // Update data cdc di database
    $stmt = $pdo->prepare("UPDATE cdcs SET nama_cdc = ?, alamat_cdc = ?, email_cdc = ? WHERE id = ?");
    $stmt->execute([$nama_cdc, $alamat_cdc, $email_cdc, $id]);

    // Set pesan sukses ke dalam session
    $_SESSION['success_message'] = 'Akun cdc berhasil diperbarui.';

    // Redirect ke halaman tampil_user_cdc.php
    header('Location: tampil_user_cdc.php');
    exit;
} else {
    // Jika bukan POST request, redirect ke halaman tampil_user_cdc.php
    header('Location: tampil_user_cdc.php');
    exit;
}
?>
