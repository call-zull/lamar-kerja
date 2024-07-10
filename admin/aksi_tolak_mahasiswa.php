<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mahasiswa_id'])) {
    $mahasiswaId = $_POST['mahasiswa_id'];
    
    // Optionally, you can delete or update the status of the mahasiswa to rejected
    // $stmt = $pdo->prepare("DELETE FROM mahasiswas WHERE user_id = ?");
    // $stmt->execute([$mahasiswaId]);
    
    // Handle success response
    echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil ditolak.']);
} else {
    // Handle error response
    echo json_encode(['status' => '
