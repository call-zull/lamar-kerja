<?php
session_start();
include '../../includes/db.php'; // Pastikan file db.php sudah termasuk koneksi PDO

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idSertifikasi = $_POST['id'];
    $mahasiswa_id = $_SESSION['mahasiswa_id']; // Ambil mahasiswa_id dari sesi

    try {
        // Fetch existing bukti paths from database
        $stmt = $pdo->prepare("SELECT bukti FROM sertifikasi WHERE id = :id AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':id', $idSertifikasi, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $buktiPaths = json_decode($result['bukti'], true);

            // Delete each bukti file if it exists and is not a URL
            if (is_array($buktiPaths)) {
                foreach ($buktiPaths as $buktiPath) {
                    if (filter_var($buktiPath, FILTER_VALIDATE_URL) === false && file_exists($buktiPath)) {
                        unlink($buktiPath);
                    }
                }
            }

            // Delete sertifikasi record from database
            $stmt = $pdo->prepare("DELETE FROM sertifikasi WHERE id = :id AND mahasiswa_id = :mahasiswa_id");
            $stmt->bindParam(':id', $idSertifikasi, PDO::PARAM_INT);
            $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Data sertifikasi berhasil dihapus.";
                header("Location: tampil_sertifikasi.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Gagal menghapus data sertifikasi.";
                header("Location: tampil_sertifikasi.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Data sertifikasi tidak ditemukan.";
            header("Location: tampil_sertifikasi.php");
            exit();
        }
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Terjadi kesalahan saat menghapus data sertifikasi: " . $e->getMessage();
        header("Location: tampil_sertifikasi.php");
        exit();
    }
} else {
    // Jika tidak ada ID yang diterima atau request bukan POST, kirimkan respons kode status 400
    $_SESSION['error_message'] = "Permintaan tidak valid.";
    header("Location: tampil_sertifikasi.php");
    exit();
}

// Tutup koneksi database (jika perlu, tergantung implementasi db.php)
// $pdo = null;
?>
