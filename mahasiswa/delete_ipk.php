<?php
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

// Get POST data
$semester = $_POST['semester'];

// Validate semester
if ($semester < 1 || $semester > 8) {
    echo "Invalid semester";
    exit;
}

// Update IPK value to null
$user_id = $_SESSION['user_id'];
$sql = "UPDATE mahasiswas SET ipk_semester_$semester = NULL WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

echo "IPK deleted successfully";
?>
