<?php
session_start();
include '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id = $_POST['id'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $alamat_perusahaan = $_POST['alamat_perusahaan'];
    $email_perusahaan = $_POST['email_perusahaan'];

    // Update data perusahaan di database
    $stmt = $pdo->prepare("UPDATE perusahaans SET nama_perusahaan = ?, alamat_perusahaan = ?, email_perusahaan = ? WHERE id = ?");
    $stmt->execute([$nama_perusahaan, $alamat_perusahaan, $email_perusahaan, $id]);

    // Set pesan sukses ke dalam session
    $_SESSION['success_message'] = 'Akun Perusahaan berhasil diperbarui.';

    // Redirect ke halaman tampil_user_perusahaan.php
    header('Location: tampil_user_perusahaan.php');
    exit;
} else {
    // Jika bukan POST request, redirect ke halaman tampil_user_perusahaan.php
    header('Location: tampil_user_perusahaan.php');
    exit;
}
?>
