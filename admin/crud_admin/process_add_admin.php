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
    $nama_admin = $_POST['nama_admin'];
    $email = $_POST['email'];
    $jurusan = $_POST['jurusan'];
    $prodi = $_POST['prodi'];

    try {
        $pdo->beginTransaction();

        // Insert into users table
        $sql_user = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'admin')";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([
            ':username' => $username,
            ':password' => $password,
        ]);

        // Get last inserted user ID
        $user_id = $pdo->lastInsertId();

        // Insert into admins table
        $sql_admin = "INSERT INTO admins (user_id, nama_admin, email, jurusan_id, prodi_id) VALUES (:user_id, :nama_admin, :email, :jurusan, :prodi)";
        $stmt_admin = $pdo->prepare($sql_admin);
        $stmt_admin->execute([
            ':user_id' => $user_id,
            ':nama_admin' => $nama_admin,
            ':email' => $email,
            ':jurusan' => $jurusan,
            ':prodi' => $prodi,
        ]);

        $pdo->commit();

        $_SESSION['success_message'] = "Akun admin berhasil ditambahkan.";
        header('Location: tampil_user_admin.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
    } else {
    header('Location: tampil_user_admin.php');
    exit;
}
?>
