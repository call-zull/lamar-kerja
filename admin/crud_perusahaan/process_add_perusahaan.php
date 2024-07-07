<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $email_perusahaan = $_POST['email_perusahaan'];
    $alamat_perusahaan = $_POST['alamat_perusahaan'];

    try {
        $pdo->beginTransaction();

        // Insert into users table
        $sql_user = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'perusahaan')";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([
            ':username' => $username,
            ':password' => $password,
        ]);

        // Get last inserted user ID
        $user_id = $pdo->lastInsertId();

        // Insert into perusahaan table
        $sql_perusahaan = "INSERT INTO perusahaans (user_id, nama_perusahaan, email_perusahaan, alamat_perusahaan) VALUES (:user_id, :nama_perusahaan, :email_perusahaan, :alamat_perusahaan)";
        $stmt_perusahaan = $pdo->prepare($sql_perusahaan);
        $stmt_perusahaan->execute([
            ':user_id' => $user_id,
            ':nama_perusahaan' => $nama_perusahaan,
            ':email_perusahaan' => $email_perusahaan,
            ':alamat_perusahaan' => $alamat_perusahaan,
        ]);

        $pdo->commit();

        $_SESSION['success_message'] = "Akun perusahaan berhasil ditambahkan.";
        header('Location: tampil_user_perusahaan.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: tampil_user_perusahaan.php');
    exit;
}
?>
