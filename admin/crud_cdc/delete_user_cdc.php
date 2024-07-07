<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cdc_id = $_POST['cdc_id'];
    $user_id = $_POST['user_id'];

    try {
        $pdo->beginTransaction();

        // Delete CDC record
        $sql = "DELETE FROM cdcs WHERE id = :cdc_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cdc_id' => $cdc_id]);

        // Delete user record
        $sql_user = "DELETE FROM users WHERE id = :user_id";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([':user_id' => $user_id]);

        $pdo->commit();

        $_SESSION['success_message'] = "Akun CDC berhasil dihapus.";
        header('Location: tampil_user_cdc.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: tampil_user_cdc.php');
    exit;
}
?>
