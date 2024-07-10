<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['perusahaan_id'])) {
    $perusahaanId = $_POST['perusahaan_id'];
    
    // Update approved status to 1
    $stmt = $pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?");
    $stmt->execute([$perusahaanId]);
    
    // Handle success response
    echo json_encode(['status' => 'success', 'message' => 'Perusahaan berhasil diterima.']);
} else {
    // Handle error response
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}
?>