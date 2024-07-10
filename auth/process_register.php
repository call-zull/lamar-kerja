<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $approved = 0; // Default approval status is 0 (not approved)

    // Validate NIM for mahasiswa
    if ($role === 'mahasiswa') {
        $nim = $_POST['nim'];

        if (!preg_match('/^[A-Ea-e]\d{9}$/', $nim)) {
            $_SESSION['error'] = 'NIM tidak valid';
            header('Location: register.php');
            exit;
        }

        // Mahasiswa harus memasukkan nomor WhatsApp
        $no_telp = $_POST['no_telp']; // Updated to use 'no_telp' input
    } elseif ($role === 'perusahaan') {
        // Perusahaan juga harus memasukkan nomor WhatsApp
        $no_telp = $_POST['no_telp']; // Updated to use 'no_telp' input
    } else {
        // Role lainnya tidak memerlukan nomor WhatsApp, jadi inisialisasi $no_telp sebagai null atau kosong
        $no_telp = null;
    }

    try {
        $pdo->beginTransaction();

        // Check if username already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Username sudah ada.';
            header('Location: register.php');
            exit;
        }

        // Insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, approved, no_telp) VALUES (:username, :password, :role, :approved, :no_telp)");
        $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'role' => $role, 'approved' => $approved, 'no_telp' => $no_telp]);
        $userId = $pdo->lastInsertId();

        if ($role === 'mahasiswa') {
            // Insert mahasiswa data (tanpa prodi_id dan jurusan_id)
            $stmtMahasiswa = $pdo->prepare("INSERT INTO mahasiswas (user_id, nim, no_telp) VALUES (:user_id, :nim, :no_telp)");
            $stmtMahasiswa->execute(['user_id' => $userId, 'nim' => $nim, 'no_telp' => $no_telp]);
            $_SESSION['success'] = 'Registrasi berhasil! Mohon tunggu persetujuan admin.';
        } elseif ($role === 'perusahaan') {
            // Insert perusahaan data
            $stmtPerusahaan = $pdo->prepare("INSERT INTO perusahaans (user_id, approved, no_telp) VALUES (:user_id, 0, :no_telp)");
            $stmtPerusahaan->execute(['user_id' => $userId, 'no_telp' => $no_telp]);
            $_SESSION['success'] = 'Registrasi berhasil. Mohon tunggu admin mengkonfirmasi akun';
        }
        

        $pdo->commit();
        header('Location: register.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Gagal melakukan registrasi: ' . $e->getMessage();
        header('Location: register.php');
        exit;
    }

} else {
    header('Location: register.php');
    exit;
}
?>
