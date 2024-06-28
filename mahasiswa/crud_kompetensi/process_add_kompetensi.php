<?php
session_start();
include '../../includes/db.php'; // Sesuaikan dengan lokasi file db.php

// Periksa koneksi database dengan PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addCompetency') {
    $namaKompetensi = $_POST['nama_kompetensi'];
    $bidangStudi = $_POST['bidang_studi'];
    $nomorSk = $_POST['nomor_sk'];
    $tahunSertifikasi = $_POST['tahun_sertifikasi'];
    $masaBerlaku = $_POST['masa_berlaku'];
    
    // Proses upload bukti
    $bukti = [];
    $targetDir = "../../assets/mahasiswa/kompetensi/";
    foreach ($_FILES['bukti']['tmp_name'] as $key => $tmp_name) {
        $fileName = basename($_FILES['bukti']['name'][$key]);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $fileExt; // Buat nama file unik
        $targetFilePath = $targetDir . $uniqueName;

        if (move_uploaded_file($tmp_name, $targetFilePath)) {
            $bukti[] = $targetFilePath;
        } else {
            echo "Gagal memindahkan file yang diunggah.";
        }
    }
    
    // Simpan data ke database menggunakan prepared statement PDO
    $jsonBukti = json_encode($bukti); // Simpan array path bukti sebagai JSON
$stmt = $pdo->prepare("INSERT INTO kompetensi (nama_kompetensi, bidang_studi, nomor_sk, tahun_sertifikasi, masa_berlaku, bukti) 
                        VALUES (:namaKompetensi, :bidangStudi, :nomorSk, :tahunSertifikasi, :masaBerlaku, :bukti)");
$stmt->bindParam(':namaKompetensi', $namaKompetensi);
$stmt->bindParam(':bidangStudi', $bidangStudi);
$stmt->bindParam(':nomorSk', $nomorSk);
$stmt->bindParam(':tahunSertifikasi', $tahunSertifikasi);
$stmt->bindParam(':masaBerlaku', $masaBerlaku);
$stmt->bindParam(':bukti', $jsonBukti);

    
    // Eksekusi statement
    try {
        $stmt->execute();
        // Jika berhasil disimpan, redirect ke halaman tampil_kompetensi.php
        $_SESSION['success_message'] = "Kompetensi berhasil ditambahkan.";
        header("Location: tampil_kompetensi.php");
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
