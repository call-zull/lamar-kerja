<?php
session_start();
include '../../includes/db.php'; // Pastikan file db.php sudah termasuk koneksi PDO

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idProyek = $_POST['id'];
    $mahasiswa_id = $_SESSION['mahasiswa_id']; // Ambil mahasiswa_id dari sesi

    try {
        // Fetch existing proyek data
        $stmt = $pdo->prepare("SELECT bukti FROM proyek WHERE id = :idProyek AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':idProyek', $idProyek, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $_SESSION['error_message'] = "Data proyek tidak ditemukan.";
            header("Location: tampil_proyek.php");
            exit();
        }

        // Hapus file bukti fisik dari server jika ada
        $buktiFiles = json_decode($result['bukti'], true);
        foreach ($buktiFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Hapus proyek dari database
        $stmt = $pdo->prepare("DELETE FROM proyek WHERE id = :idProyek AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':idProyek', $idProyek, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Proyek berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus proyek.";
        }

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Terjadi kesalahan saat menghapus proyek: " . $e->getMessage();
    }
}

header("Location: tampil_proyek.php");
exit();
?>
