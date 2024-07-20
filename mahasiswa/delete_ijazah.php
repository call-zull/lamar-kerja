<?php
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $ijazah = $_POST['ijazah'];

    $file_path = "../assets/mahasiswa/ijazahatautranskip/" . $ijazah;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Update database
    $sql = "UPDATE mahasiswas SET ijazah = NULL WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    $_SESSION['message'] = "File berhasil dihapus.";
    header('Location: ijazah_transkrip.php');
    exit;
}
?>
