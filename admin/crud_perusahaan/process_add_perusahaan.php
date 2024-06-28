<?php
session_start();
include '../../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $alamat_perusahaan = $_POST['alamat_perusahaan'];
    $email_perusahaan = $_POST['email_perusahaan'];

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Mulai transaksi
        $pdo->beginTransaction();

        // Masukkan data ke dalam tabel users
        $sql_users = "INSERT INTO users (username, password, role) 
                      VALUES (?, ?, 'perusahaan')";
        $stmt_users = $pdo->prepare($sql_users);
        $stmt_users->execute([$username, $hashed_password]);

        // Ambil ID user yang baru saja dimasukkan
        $user_id = $pdo->lastInsertId();

        // Masukkan data admin ke dalam tabel admins
        $sql_perusahaans = "INSERT INTO perusahaans (user_id, nama_perusahaan, alamat_perusahaan, email_perusahaan) VALUES (?, ?, ?, ?)";
        $stmt_perusahaans = $pdo->prepare($sql_perusahaans);
        $stmt_perusahaans->execute([$user_id, $nama_perusahaan, $alamat_perusahaan, $email_perusahaan]);

        // Commit transaksi jika berhasil
        $pdo->commit();

        // Set pesan sukses ke dalam session
        $_SESSION['success_message'] = 'Akun Perusahaan berhasil ditambahkan.';

        // Redirect kembali ke halaman admin setelah berhasil
        header("Location: tampil_user_perusahaan.php");
        exit();
    } catch (PDOException $e) {
        // Rollback transaksi jika terjadi error
        $pdo->rollBack();
        die("Gagal menambahkan Perusahaan: " . $e->getMessage());
    }
} else {
    // Redirect jika tidak diakses dari form
    header("Location: tampil_user_perusahaan.php");
    exit();
}
?>
