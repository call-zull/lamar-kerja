<?php
session_start();
include '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaProyek = $_POST['nama_proyek'];
    $partner = $_POST['partner'];
    $peran = $_POST['peran'];
    $waktuAwal = $_POST['waktu_awal'];
    $waktuSelesai = $_POST['waktu_selesai'];
    $tujuanProyek = $_POST['tujuan_proyek'];
    $mahasiswaId = $_SESSION['mahasiswa_id'];

    $bukti = [];
    $targetDir = "../../assets/mahasiswa/proyek/";

    // Proses upload file bukti
    if (!empty($_FILES['bukti_file']['tmp_name'])) {
        $fileName = basename($_FILES['bukti_file']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $fileExt; // Buat nama file unik
        $targetFilePath = $targetDir . $uniqueName;

        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $targetFilePath)) {
            $bukti[] = $targetFilePath;
        } else {
            echo "Gagal memindahkan file yang diunggah.";
        }
    }

    // Proses link Google Drive
    if (!empty($_POST['bukti_link']) && $_POST['bukti_link'] !== '-') {
        $bukti[] = $_POST['bukti_link'];
    }

    // Simpan data ke database menggunakan prepared statement PDO
    $jsonBukti = json_encode($bukti); // Simpan array path bukti sebagai JSON
    $stmt = $pdo->prepare("INSERT INTO proyek (nama_proyek, partner, peran, waktu_awal, waktu_selesai, tujuan_proyek, bukti, mahasiswa_id) 
                            VALUES (:namaProyek, :partner, :peran, :waktuAwal, :waktuSelesai, :tujuanProyek, :bukti, :mahasiswaId)");
    $stmt->bindParam(':namaProyek', $namaProyek);
    $stmt->bindParam(':partner', $partner);
    $stmt->bindParam(':peran', $peran);
    $stmt->bindParam(':waktuAwal', $waktuAwal);
    $stmt->bindParam(':waktuSelesai', $waktuSelesai);
    $stmt->bindParam(':tujuanProyek', $tujuanProyek);
    $stmt->bindParam(':bukti', $jsonBukti);
    $stmt->bindParam(':mahasiswaId', $mahasiswaId);

    // Eksekusi statement
    try {
        $stmt->execute();
        // Jika berhasil disimpan, redirect ke halaman tampil_proyek.php
        $_SESSION['success_message'] = "Proyek berhasil ditambahkan.";
        header("Location: tampil_proyek.php");
        exit();
    } catch (PDOException $e) {
        // Jika terjadi error, tampilkan pesan error
        echo "Error: " . $e->getMessage();
    }
    
    // Tutup statement
    $stmt = null;
}

// Tutup koneksi database
$pdo = null;
?>
