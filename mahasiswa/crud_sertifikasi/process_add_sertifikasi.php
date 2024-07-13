<?php
session_start();
include '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $namaSertifikasi = $_POST['nama_sertifikasi'];
    $lembagaId = $_POST['lembaga_id'];
    $nomorSk = $_POST['nomor_sk'];
    $tanggalDiperoleh = $_POST['tanggal_diperoleh'];
    $tanggalKadaluarsa = isset($_POST['tanggal_kadaluarsa']) ? $_POST['tanggal_kadaluarsa'] : NULL;
    $mahasiswaId = isset($_SESSION['mahasiswa_id']) ? $_SESSION['mahasiswa_id'] : null;

    $bukti = [];
    $targetDir = "../../assets/mahasiswa/sertifikasi/";

    // Proses upload file bukti
    if (!empty($_FILES['bukti_file']['tmp_name'])) {
        $fileName = basename($_FILES['bukti_file']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $fileExt; // Buat nama file unik
        $targetFilePath = $targetDir . $uniqueName;

        // Cek dan pindahkan file yang diunggah
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $targetFilePath)) {
            $bukti[] = $targetFilePath;
        } else {
            echo "Gagal memindahkan file yang diunggah.";
            exit();
        }
    }

    // Proses link bukti
    if (!empty($_POST['bukti_link']) && $_POST['bukti_link'] !== '-') {
        $bukti[] = $_POST['bukti_link'];
    }

    // Simpan array path bukti sebagai JSON
    $jsonBukti = json_encode($bukti);

    // Siapkan statement untuk menyimpan data ke database
    $stmt = $pdo->prepare("INSERT INTO sertifikasi (nama_sertifikasi, lembaga_id, nomor_sk, tanggal_diperoleh, tanggal_kadaluarsa, bukti, mahasiswa_id) 
                            VALUES (:namaSertifikasi, :lembagaId, :nomorSk, :tanggalDiperoleh, :tanggalKadaluarsa, :bukti, :mahasiswaId)");

    // Bind parameter ke statement
    $stmt->bindParam(':namaSertifikasi', $namaSertifikasi);
    $stmt->bindParam(':lembagaId', $lembagaId);
    $stmt->bindParam(':nomorSk', $nomorSk);
    $stmt->bindParam(':tanggalDiperoleh', $tanggalDiperoleh);
    $stmt->bindParam(':tanggalKadaluarsa', $tanggalKadaluarsa);
    $stmt->bindParam(':bukti', $jsonBukti);
    $stmt->bindParam(':mahasiswaId', $mahasiswaId, PDO::PARAM_INT);

    // Eksekusi statement dalam blok try-catch untuk menangani error
    try {
        $stmt->execute();
        // Jika berhasil disimpan, redirect ke halaman tampil_sertifikasi.php
        $_SESSION['success_message'] = "Sertifikasi berhasil ditambahkan.";
        header("Location: tampil_sertifikasi.php");
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
