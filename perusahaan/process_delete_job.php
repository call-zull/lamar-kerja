<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['hapus_id'];

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Delete related entries in lamaran_mahasiswas
        $sql = "DELETE FROM lamaran_mahasiswas WHERE lowongan_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Delete the job listing
        $sql = "DELETE FROM lowongan_kerja WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Commit transaction
        $pdo->commit();

        $_SESSION['success_message'] = "Lowongan berhasil dihapus.";
        header('Location: lowongan_kerja.php');
        exit;
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>