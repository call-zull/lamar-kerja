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
    $semester = $_POST['khs_semester'];
    $khs_file = $_POST['khs_file'];

    $file_path = "../assets/mahasiswa/khs/" . $khs_file;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Update database
    $sql = "UPDATE mahasiswas SET khs_semester_$semester = NULL WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    $_SESSION['message'] = "File KHS Semester $semester berhasil dihapus.";
    header('Location: kartu_hasil_studi.php');
    exit;
}
?>
