<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['edit_id'];
    $nama_pelamar = $_POST['edit_nama_pelamar'];
    $status = $_POST['edit_status'];

    try {
        $sql = "UPDATE lamaran_mahasiswas SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['status' => $status, 'id' => $id]);

        $_SESSION['message'] = 'Data pelamar berhasil diperbarui.';
        header('Location: lowongan_kerja.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
<<<<<<< HEAD
?>
=======
?>
>>>>>>> origin/main
