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
    $nama_cdc = $_POST['nama_cdc'];
    $alamat_cdc = $_POST['alamat_cdc'];
    $email_cdc = $_POST['email_cdc'];

    try {
        $pdo->beginTransaction();

        // Insert into users table with approved = 1
        $sql_user = "INSERT INTO users (username, password, role, approved) VALUES (:username, :password, 'cdc', 1)";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([
            ':username' => $username,
            ':password' => $password,
        ]);

        // Get last inserted user ID
        $user_id = $pdo->lastInsertId();

        // Insert into cdcs table with approved value from users
        $sql_cdc = "INSERT INTO cdcs (user_id, nama_cdc, alamat_cdc, email_cdc, approved) 
                    VALUES (:user_id, :nama_cdc, :alamat_cdc, :email_cdc, (SELECT approved FROM users WHERE id = :user_id))";
        $stmt_cdc = $pdo->prepare($sql_cdc);
        $stmt_cdc->execute([
            ':user_id' => $user_id,
            ':nama_cdc' => $nama_cdc,
            ':alamat_cdc' => $alamat_cdc,
            ':email_cdc' => $email_cdc,
        ]);

        $pdo->commit();

        $_SESSION['success_message'] = "Akun CDC berhasil ditambahkan.";
        header('Location: tampil_user_cdc.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: tampil_user_cdc.php');
    exit;
}
?>
