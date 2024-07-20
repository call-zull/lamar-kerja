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
$ipk = $_POST['ipk'];

// Update IPK value
$user_id = $_SESSION['user_id'];
$sql = "UPDATE mahasiswas SET ipk = ? WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$ipk, $user_id]);

echo "Overall IPK updated successfully";
