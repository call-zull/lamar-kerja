<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Pastikan ini hanya dapat diakses melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    // Ambil data dari POST
    $username = $_POST['username'];

    // Lakukan aksi tolak perusahaan (misalnya, ubah status approved menjadi -1 atau hapus dari database)
    // Implementasi aksi tolak sesuai kebutuhan aplikasi Anda

    // Contoh sederhana: Ubah status approved menjadi -1
    $stmt = $pdo->prepare("UPDATE users SET approved = -1 WHERE username = :username");
    $stmt->execute(['username' => $username]);

    // Response jika berhasil
    echo json_encode(['status' => 'success', 'message' => 'Perusahaan berhasil ditolak']);
    exit;
} else {
    // Response jika request tidak valid
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}
?>
