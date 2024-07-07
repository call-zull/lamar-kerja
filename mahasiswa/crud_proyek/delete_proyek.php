<?php
session_start();
include '../../includes/db.php'; // Pastikan file db.php sudah termasuk koneksi PDO

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    http_response_code(403);
    echo json_encode(array("message" => "Akses ditolak."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idProyek = $_POST['id'];
    $mahasiswa_id = $_SESSION['mahasiswa_id']; // Ambil mahasiswa_id dari sesi

    try {
        // Fetch existing bukti paths from database
        $stmt = $pdo->prepare("SELECT bukti FROM proyek WHERE id = :id AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':id', $idProyek);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $buktiPaths = json_decode($result['bukti'], true);

            // Delete each bukti file if it exists and is not a URL
            foreach ($buktiPaths as $buktiPath) {
                if (filter_var($buktiPath, FILTER_VALIDATE_URL) === false && file_exists($buktiPath)) {
                    unlink($buktiPath);
                }
            }

            // Delete proyek record from database
            $stmt = $pdo->prepare("DELETE FROM proyek WHERE id = :id AND mahasiswa_id = :mahasiswa_id");
            $stmt->bindParam(':id', $idProyek);
            $stmt->bindParam(':mahasiswa_id', $mahasiswa_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Data proyek berhasil dihapus.";
                header("Location: tampil_proyek.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Gagal menghapus data proyek.";
                header("Location: tampil_proyek.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Data proyek tidak ditemukan.";
            header("Location: tampil_proyek.php");
            exit();
        }
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Terjadi kesalahan saat menghapus data proyek: " . $e->getMessage();
        header("Location: tampil_proyek.php");
        exit();
    }
}

// Tutup koneksi database (jika perlu, tergantung implementasi db.php)
// $pdo = null;
?>
