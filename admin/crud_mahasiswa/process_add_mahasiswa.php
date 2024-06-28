<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari formulir
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $nim = $_POST['nim'];
    $nama_mahasiswa = $_POST['nama_mahasiswa'];
    $jurusan = $_POST['jurusan'];
    $prodi = $_POST['prodi'];
    $tahun_masuk = $_POST['tahun_masuk'];
    $status = $_POST['status'];
    $jk = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];

    try {
        $pdo->beginTransaction();

        // Insert into users table
        $sql_user = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'mahasiswa')";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([
            ':username' => $username,
            ':password' => $hashed_password, // Simpan password yang telah di-hash
        ]);

        // Get last inserted user ID
        $user_id = $pdo->lastInsertId();

        // Insert into mahasiswas table
        $sql_mahasiswa = "INSERT INTO mahasiswas (user_id, nim, nama_mahasiswa, jurusan_id, prodi_id, tahun_masuk, status, jk, alamat, email, no_telp) 
                         VALUES (:user_id, :nim, :nama_mahasiswa, :jurusan, :prodi, :tahun_masuk, :status, :jk, :alamat, :email, :no_telp)";
        $stmt_mahasiswa = $pdo->prepare($sql_mahasiswa);
        $stmt_mahasiswa->execute([
            ':user_id' => $user_id,
            ':nim' => $nim,
            ':nama_mahasiswa' => $nama_mahasiswa,
            ':jurusan' => $jurusan,
            ':prodi' => $prodi,
            ':tahun_masuk' => $tahun_masuk,
            ':status' => $status,
            ':jk' => $jk,
            ':alamat' => $alamat,
            ':email' => $email,
            ':no_telp' => $no_telp,
        ]);

        $pdo->commit();

        $_SESSION['success_message'] = "Akun mahasiswa berhasil ditambahkan.";
        header('Location: tampil_user_mahasiswa.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: tampil_user_mahasiswa.php');
    exit;
}

?>
