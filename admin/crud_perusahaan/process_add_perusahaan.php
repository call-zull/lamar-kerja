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
    $approved = 0;

    // Jika yang membuat akun adalah admin, set nilai approved menjadi 1
    if ($_SESSION['role'] == 'admin') {
        $approved = 1;
    }

    try {
        $pdo->beginTransaction();

        // Insert into users table
        $sql_user = "INSERT INTO users (username, password, role, approved) VALUES (:username, :password, 'perusahaan', :approved)";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([
            ':username' => $username,
            ':password' => $password,
            ':approved' => $approved,
        ]);

        // Get last inserted user ID
        $user_id = $pdo->lastInsertId();

        // Insert into perusahaan table
        $sql_perusahaan = "INSERT INTO perusahaans (user_id, nama_perusahaan, email_perusahaan, alamat_perusahaan, approved) 
        VALUES (:user_id, :nama_perusahaan, :email_perusahaan, :alamat_perusahaan, :approved)";
        $stmt_perusahaan = $pdo->prepare($sql_perusahaan);
        $stmt_perusahaan->execute([
            ':user_id' => $user_id,
            ':nama_perusahaan' => $nama_perusahaan,
            ':email_perusahaan' => $email_perusahaan,
            ':alamat_perusahaan' => $alamat_perusahaan,
            ':approved' => $approved,
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
