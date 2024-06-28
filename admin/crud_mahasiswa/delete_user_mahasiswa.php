<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil mahasiswa_id dan user_id dari POST request
    $mahasiswa_id = $_POST['mahasiswa_id'];
    $user_id = $_POST['user_id'];

    try {
        $pdo->beginTransaction();

        // Delete Mahasiswa record
        $sql = "DELETE FROM mahasiswas WHERE id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':mahasiswa_id' => $mahasiswa_id]);

        // Delete User record
        $sql_user = "DELETE FROM users WHERE id = :user_id";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([':user_id' => $user_id]);

        $pdo->commit();

        $_SESSION['success_message'] = "Akun mahasiswa berhasil dihapus.";
        header('Location: tampil_user_mahasiswa.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: tampil_user_mahasiswa.php');
    exit;
}
?>
