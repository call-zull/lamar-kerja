<?php
session_start();
include '../../includes/db.php'; 


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaSertifikasi = $_POST['nama_sertifikasi'];
    $lembagaId = $_POST['lembaga_id'];
    $nomorSk = $_POST['nomor_sk'];
    $tanggalDiperoleh = $_POST['tanggal_diperoleh'];
    $tanggalKadaluarsa = $_POST['tanggal_kadaluarsa'];

    $bukti = [];
    $targetDir = "../../assets/mahasiswa/sertifikasi/";

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
    $stmt = $pdo->prepare("INSERT INTO sertifikasi (nama_sertifikasi, lembaga_id, nomor_sk, tanggal_diperoleh, tanggal_kadaluarsa, bukti) 
                            VALUES (:namaSertifikasi, :lembagaId, :nomorSk, :tanggalDiperoleh, :tanggalKadaluarsa, :bukti)");
    $stmt->bindParam(':namaSertifikasi', $namaSertifikasi);
    $stmt->bindParam(':lembagaId', $lembagaId);
    $stmt->bindParam(':nomorSk', $nomorSk);
    $stmt->bindParam(':tanggalDiperoleh', $tanggalDiperoleh);
    $stmt->bindParam(':tanggalKadaluarsa', $tanggalKadaluarsa);
    $stmt->bindParam(':bukti', $jsonBukti);

    // Eksekusi statement
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
