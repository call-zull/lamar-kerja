<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_cdc = $_POST['nama_cdc'];
    $alamat_cdc = $_POST['alamat_cdc'];
    $email_cdc = $_POST['email_cdc'];

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Mulai transaksi
        $pdo->beginTransaction();

        // Masukkan data ke dalam tabel users
        $sql_users = "INSERT INTO users (username, password, role) 
                      VALUES (?, ?, 'cdc')";
        $stmt_users = $pdo->prepare($sql_users);
        $stmt_users->execute([$username, $hashed_password]);

        // Ambil ID user yang baru saja dimasukkan
        $user_id = $pdo->lastInsertId();

        // Masukkan data admin ke dalam tabel admins
        $sql_cdcs = "INSERT INTO cdcs (user_id, nama_cdc, alamat_cdc, email_cdc) VALUES (?, ?, ?, ?)";
        $stmt_cdcs = $pdo->prepare($sql_cdcs);
        $stmt_cdcs->execute([$user_id, $nama_cdc, $alamat_cdc, $email_cdc]);

        // Commit transaksi jika berhasil
        $pdo->commit();

        // Set pesan sukses ke dalam session
        $_SESSION['success_message'] = 'Akun CDC berhasil ditambahkan.';

        // Redirect kembali ke halaman admin setelah berhasil
        header("Location: ../admin/tampil_user_cdc.php");
        exit();
    } catch (PDOException $e) {
        // Rollback transaksi jika terjadi error
        $pdo->rollBack();
        die("Gagal menambahkan CDC: " . $e->getMessage());
    }
} else {
    // Redirect jika tidak diakses dari form
    header("Location: ../admin/tampil_user_perusahaan.php");
    exit();
}
?>
