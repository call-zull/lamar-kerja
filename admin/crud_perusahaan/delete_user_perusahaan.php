<?php
session_start();
include '../../includes/db.php';

// Periksa apakah pengguna telah login sebagai admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

// Periksa validitas ID perusahaan
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = 'ID Perusahaan tidak valid.';
    header('Location: tampil_user_perusahaan.php');
    exit;
}

$perusahaan_id = $_GET['id'];
$user_id = $_GET['user_id'];

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // Query untuk menghapus perusahaan berdasarkan ID
    $sql_delete_perusahaan = "DELETE FROM perusahaans WHERE id = ?";
    $stmt_perusahaan = $pdo->prepare($sql_delete_perusahaan);
    $stmt_perusahaan->execute([$perusahaan_id]);

    // Query untuk menghapus user berdasarkan ID
    $sql_delete_user = "DELETE FROM users WHERE id = ?";
    $stmt_user = $pdo->prepare($sql_delete_user);
    $stmt_user->execute([$user_id]);

    // Komit transaksi
    $pdo->commit();

    // Periksa apakah ada baris yang terpengaruh (perusahaan berhasil dihapus)
    if ($stmt_perusahaan->rowCount() > 0) {
        // Setelah berhasil hapus, atur pesan sukses ke dalam session
        $_SESSION['success_message'] = 'Akun Perusahaan berhasil dihapus.';
    } else {
        $_SESSION['error_message'] = 'Gagal menghapus akun Perusahaan. Data tidak ditemukan.';
    }
} catch (PDOException $e) {
    // Rollback transaksi jika terjadi kesalahan
    $pdo->rollBack();
    // Tangani kesalahan database jika terjadi
    $_SESSION['error_message'] = 'Gagal menghapus akun Perusahaan: ' . $e->getMessage();
}

// Redirect kembali ke halaman tampil user perusahaan setelah penghapusan
header('Location: tampil_user_perusahaan.php');
exit;
?>
