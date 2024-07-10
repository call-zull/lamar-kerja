<?php
// Include your database connection
include '../includes/db.php';

// Check if mahasiswa_id is set and not empty
if (isset($_POST['mahasiswa_id']) && !empty($_POST['mahasiswa_id'])) {
    $mahasiswaId = $_POST['mahasiswa_id'];

    // Update status mahasiswa menjadi diterima (approved = 1)
    $stmt = $pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?");
    if ($stmt->execute([$mahasiswaId])) {
        // Jika berhasil diupdate, kirim respons berhasil
        echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil diterima.']);
    } else {
        // Jika gagal diupdate, kirim respons error
        echo json_encode(['status' => 'error', 'message' => 'Gagal menerima mahasiswa.']);
    }
} else {
    // Jika mahasiswa_id tidak diset, kirim respons error
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak valid.']);
}
?>
