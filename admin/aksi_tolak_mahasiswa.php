<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mahasiswa_id'])) {
    $mahasiswaId = $_POST['mahasiswa_id'];
    
    // Update approved status to 2
    $stmt = $pdo->prepare("UPDATE mahasiswas SET approved = 2 WHERE user_id = ?");
    $stmt->execute([$mahasiswaId]);
    
    // Optionally, update user status
    $stmt_user = $pdo->prepare("UPDATE users SET approved = 2 WHERE id = ?");
    $stmt_user->execute([$mahasiswaId]);
    
    // Handle success response
    echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil ditolak.']);
} else {
    // Handle error response
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}
?>
