<?php
session_start();
include '../../includes/db.php'; // Pastikan file db.php sudah termasuk koneksi PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateCompetency') {
    $idKompetensi = $_POST['id_kompetensi'];
    $namaKompetensi = $_POST['nama_kompetensi'];
    $bidangStudi = $_POST['bidang_studi'];
    $nomorSk = $_POST['nomor_sk'];
    $tahunSertifikasi = $_POST['tahun_sertifikasi'];
    $masaBerlaku = $_POST['masa_berlaku'];
    
    // Proses upload bukti
    $bukti = [];
    $targetDir = "../../assets/mahasiswa/kompetensi/"; // Ubah target directory sesuai dengan struktur folder Anda
    foreach ($_FILES['bukti']['tmp_name'] as $key => $tmp_name) {
        $fileName = basename($_FILES['bukti']['name'][$key]);
        $targetFilePath = $targetDir . $fileName;
        if (move_uploaded_file($tmp_name, $targetFilePath)) {
            $bukti[] = $targetFilePath;
        }
    }
    
    try {
        // Update data kompetensi di database menggunakan PDO
        $stmt = $pdo->prepare("UPDATE kompetensi SET nama_kompetensi = :nama_kompetensi, bidang_studi = :bidang_studi, 
                                nomor_sk = :nomor_sk, tahun_sertifikasi = :tahun_sertifikasi, masa_berlaku = :masa_berlaku, bukti = :bukti 
                                WHERE id = :id_kompetensi");
        $stmt->bindParam(':nama_kompetensi', $namaKompetensi);
        $stmt->bindParam(':bidang_studi', $bidangStudi);
        $stmt->bindParam(':nomor_sk', $nomorSk);
        $stmt->bindParam(':tahun_sertifikasi', $tahunSertifikasi);
        $stmt->bindParam(':masa_berlaku', $masaBerlaku);
        $stmt->bindParam(':bukti', json_encode($bukti)); // Simpan array bukti dalam format JSON
        $stmt->bindParam(':id_kompetensi', $idKompetensi);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Data kompetensi berhasil diperbarui.";
            header("Location: tampil_kompetensi.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui data kompetensi.";
            header("Location: tampil_kompetensi.php");
            exit();
        }
        
        // Tutup statement
        $stmt = null; // Tutup statement dengan mengosongkan objek
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Terjadi kesalahan saat memperbarui data kompetensi: " . $e->getMessage();
        header("Location: tampil_kompetensi.php");
        exit();
    }
}

// Tutup koneksi database (jika perlu, tergantung implementasi db.php)
// $pdo = null;
?>
