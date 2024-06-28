<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_POST['admin_id'];
    $nama_admin = $_POST['nama_admin'];
    $email = $_POST['email'];
    $jurusan = $_POST['jurusan'];
    $prodi = $_POST['prodi'];

    try {
        $sql = "UPDATE admins SET nama_admin = :nama_admin, email = :email, jurusan_id = :jurusan, prodi_id = :prodi WHERE id = :admin_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama_admin' => $nama_admin,
            ':email' => $email,
            ':jurusan' => $jurusan,
            ':prodi' => $prodi,
            ':admin_id' => $admin_id,
        ]);

        $_SESSION['success_message'] = "Akun admin berhasil diupdate.";
        header('Location: tampil_user_admin.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: tampil_user_admin.php');
    exit;
}
?>
