<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mahasiswa_id = $_POST['mahasiswa_id'];
    $nim = $_POST['nim'];
    $nama_mahasiswa = $_POST['nama_mahasiswa'];
    $jurusan_id = $_POST['jurusan'];
    $prodi_id = $_POST['prodi'];
    $tahun_masuk = $_POST['tahun_masuk'];
    $status = $_POST['status'];
    $jk = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];

    try {
        $sql = "UPDATE mahasiswas SET nim = :nim, nama_mahasiswa = :nama_mahasiswa, jurusan_id = :jurusan, prodi_id = :prodi WHERE id = :mahasiswa_id, tahun_masuk = :tahun_masuk, status = :status, jk = :jk, alamat = :alamat, email = :email, no_telp = :no_telp";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nim' => $nim,
            ':nama_mahasiswa' => $nama_mahasiswa,
            ':nama_jurusan' => $nama_jurusan,
            ':prodi' => $prodi,
            ':tahun_masuk' => $tahun_masuk,
            ':status' => $status,
            ':jk' => $jk,
            ':alamat' => $alamat,
            ':email' => $email,
            ':no_telp' => $no_telp,
            ':mahasiswa_id' => $mahasiswa_id,
        ]);

        $_SESSION['success_message'] = "Akun mahasiswa berhasil diupdate.";
        header('Location: tampil_user_mahasiswa.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: tampil_user_mahasiswa.php');
    exit;
}
?>
