<?php
session_start();

// Check if user is authenticated as mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mahasiswa_id = $_POST['id'];
    $nim = $_POST['nim'];
    $nama_mahasiswa = $_POST['nama_mahasiswa'];
    $email = $_POST['email'];
    $prodi_id = $_POST['prodi_id'];
    $jurusan_id = $_POST['jurusan_id'];
    $tahun_masuk = $_POST['tahun_masuk'];
    $status = $_POST['status'];
    $jk = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $keahlian = $_POST['keahlian'];



    // Update mahasiswa profile data in database
    try {
        $sql = "UPDATE mahasiswas 
                SET nim = ?, nama_mahasiswa = ?, email = ?, prodi_id = ?, jurusan_id = ?, tahun_masuk = ?, status = ?, jk = ?, alamat = ?, no_telp = ?, keahlian = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nim, $nama_mahasiswa, $email, $prodi_id, $jurusan_id, $tahun_masuk, $status, $jk, $alamat, $no_telp, $keahlian, $mahasiswa_id]);

        // Redirect to profile page after update
        header('Location: profile_mahasiswa.php');
        exit;
    } catch (PDOException $e) {
        // Display the error message
        echo "Error: " . $e->getMessage();
    }
}
?>
