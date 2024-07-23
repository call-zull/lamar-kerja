<?php
session_start();

// Memastikan pengguna login sebagai mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Sertakan koneksi database
include '../includes/db.php';

// Ambil data POST
$semester = $_POST['semester'];

// Validasi semester
if ($semester < 1 || $semester > 8) {
    echo "<script>alert('Invalid semester'); window.location.href = 'previous_page.php';</script>";
    exit;
}

// Update nilai IPK menjadi null
$user_id = $_SESSION['user_id'];
$sql = "UPDATE mahasiswas SET ipk_semester_$semester = NULL WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

echo "<script>alert('IPK deleted successfully'); window.location.href = 'previous_page.php';</script>";
?>

?>