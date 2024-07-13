<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$new_skills = $_POST['keahlian'] ?? '';

$sql = "UPDATE mahasiswas SET keahlian = ? WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
if ($stmt->execute([$new_skills, $user_id])) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>
