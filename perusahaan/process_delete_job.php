<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['hapus_id'];

    try {
        $sql = "DELETE FROM lowongan_kerja WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $_SESSION['success_message'] = "Lowongan berhasil dihapus.";
        header('Location: lowongan_kerja.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
