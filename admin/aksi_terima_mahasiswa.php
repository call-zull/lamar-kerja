<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mahasiswa_id'])) {
    $mahasiswaId = $_POST['mahasiswa_id'];
    
    // Update approved status to 1
    $stmt = $pdo->prepare("UPDATE mahasiswas SET approved = 1 WHERE user_id = ?");
    $stmt->execute([$mahasiswaId]);
    
    // Update user approved status
    $stmt_user = $pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?");
    $stmt_user->execute([$mahasiswaId]);
    
    // Handle success response
    echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil diterima.']);
} else {
    // Handle error response
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}
?>
