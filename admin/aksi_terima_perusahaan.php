<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['perusahaan_id'])) {
    $perusahaanId = $_POST['perusahaan_id'];
    
    // Update approved status to 1
    $stmt = $pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?");
    $stmt->execute([$perusahaanId]);
    
    // Handle success or error response
    echo "Perusahaan berhasil diterima.";
} else {
    echo "Permintaan tidak valid.";
}
?>
