<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Function to delete a job listing
function deleteJob($pdo, $id) {
    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Delete related entries in lamaran_mahasiswas
        $sql = "DELETE FROM lamaran_mahasiswas WHERE lowongan_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Delete the job listing
        $sql = "DELETE FROM lowongan_kerja WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Commit transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        error_log("Error deleting job: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['hapus_id']);

    if (deleteJob($pdo, $id)) {
        $_SESSION['success_message'] = "Lowongan berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan saat menghapus lowongan.";
    }
    
    header('Location: lowongan_kerja.php');
    exit;
}
?>
