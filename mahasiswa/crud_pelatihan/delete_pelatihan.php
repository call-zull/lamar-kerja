<?php
session_start();
include '../../includes/db.php'; // Pastikan file db.php sudah termasuk koneksi PDO

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idPelatihan = $_POST['id'];
    $mahasiswa_id = $_SESSION['mahasiswa_id']; // Ambil mahasiswa_id dari sesi

    try {
        // Fetch existing bukti paths from database
        $stmt = $pdo->prepare("SELECT bukti FROM pelatihan WHERE id_pelatihan = :id AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':id', $idPelatihan, PDO::PARAM_INT);
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

            // Delete pelatihan record from database
            $stmt = $pdo->prepare("DELETE FROM pelatihan WHERE id_pelatihan = :id AND mahasiswa_id = :mahasiswa_id");
            $stmt->bindParam(':id', $idPelatihan, PDO::PARAM_INT);
            $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Data pelatihan berhasil dihapus.";
                header("Location: tampil_pelatihan.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Gagal menghapus data pelatihan.";
                header("Location: tampil_pelatihan.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Data pelatihan tidak ditemukan.";
            header("Location: tampil_pelatihan.php");
            exit();
        }
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Terjadi kesalahan saat menghapus data pelatihan: " . $e->getMessage();
        header("Location: tampil_pelatihan.php");
        exit();
    }
} else {
    // Jika tidak ada ID yang diterima atau request bukan POST, kirimkan respons kode status 400
    $_SESSION['error_message'] = "Permintaan tidak valid.";
    header("Location: tampil_pelatihan.php");
    exit();
}

// Tutup koneksi database (jika perlu, tergantung implementasi db.php)
// $pdo = null;
?>
