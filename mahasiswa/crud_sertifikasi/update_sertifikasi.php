<?php
session_start();
include '../../includes/db.php'; // Pastikan file db.php sudah termasuk koneksi PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idSertifikasi = $_POST['id'];
    $namaSertifikasi = $_POST['nama_sertifikasi'];
    $lembagaId = $_POST['lembaga_id'];
    $nomorSk = $_POST['nomor_sk'];
    $tanggalDiperoleh = $_POST['tanggal_diperoleh'];
    $tanggalKadaluarsa = $_POST['tanggal_kadaluarsa'];

    // Proses upload bukti
    $bukti = [];

    // Jika ada file bukti yang diunggah, lakukan proses upload
    if (!empty($_FILES['bukti_file']['tmp_name'])) {
        $targetDir = "../../assets/mahasiswa/sertifikasi/";

        $fileName = basename($_FILES['bukti_file']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $fileExt; // Buat nama file unik
        $targetFilePath = $targetDir . $uniqueName;

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $targetFilePath)) {
            $bukti[] = $targetFilePath;
        } else {
            $_SESSION['error_message'] = "Gagal mengunggah file bukti.";
            header("Location: tampil_sertifikasi.php");
            exit();
        }
    }

    // Proses link Google Drive
    if (!empty($_POST['bukti_link']) && $_POST['bukti_link'] !== '-') {
        $bukti[] = $_POST['bukti_link'];
    }

    try {
        // Fetch existing sertifikasi data if available
        $stmt = $pdo->prepare("SELECT bukti FROM sertifikasi WHERE id = :id");
        $stmt->bindParam(':id', $idSertifikasi);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            $_SESSION['error_message'] = "Data sertifikasi tidak ditemukan.";
            header("Location: tampil_sertifikasi.php");
            exit();
        }

        $existingBukti = json_decode($result['bukti'], true);

        // If new bukti is uploaded or provided, replace the existing one
        if (!empty($bukti)) {
            $existingBukti = $bukti;
        }

        // Update data sertifikasi di database menggunakan PDO
        $stmt = $pdo->prepare("UPDATE sertifikasi SET nama_sertifikasi = :namaSertifikasi, lembaga_id = :lembagaId, 
                                nomor_sk = :nomorSk, tanggal_diperoleh = :tanggalDiperoleh, tanggal_kadaluarsa = :tanggalKadaluarsa, 
                                bukti = :bukti WHERE id = :idSertifikasi");
        $stmt->bindParam(':namaSertifikasi', $namaSertifikasi);
        $stmt->bindParam(':lembagaId', $lembagaId);
        $stmt->bindParam(':nomorSk', $nomorSk);
        $stmt->bindParam(':tanggalDiperoleh', $tanggalDiperoleh);
        $stmt->bindParam(':tanggalKadaluarsa', $tanggalKadaluarsa);
        $stmt->bindParam(':bukti', json_encode($existingBukti)); // Simpan array bukti dalam format JSON
        $stmt->bindParam(':idSertifikasi', $idSertifikasi);
        
        // Eksekusi statement
        if ($stmt->execute()) {
            // Jika berhasil disimpan, redirect ke halaman tampil_sertifikasi.php
            $_SESSION['success_message'] = "Data sertifikasi berhasil diperbarui.";
            header("Location: tampil_sertifikasi.php");
            exit();
        } else {
            // Jika gagal disimpan, set pesan error
            $_SESSION['error_message'] = "Gagal memperbarui data sertifikasi.";
            header("Location: tampil_sertifikasi.php");
            exit();
        }
        
        // Tutup statement
        $stmt = null; // Tutup statement dengan mengosongkan objek
        
    } catch (PDOException $e) {
        // Tangani kesalahan PDO
        $_SESSION['error_message'] = "Terjadi kesalahan saat memperbarui data sertifikasi: " . $e->getMessage();
        header("Location: tampil_sertifikasi.php");
        exit();
    }
}

// Tutup koneksi database (jika perlu, tergantung implementasi db.php)
// $pdo = null;
?>
