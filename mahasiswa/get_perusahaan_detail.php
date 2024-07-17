<?php
include '../includes/db.php';

if (isset($_GET['user_id'])) {
    $perusahaan_id = $_GET['user_id'];

    try {
        $sql = "SELECT * FROM perusahaans WHERE id = :perusahaan_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['perusahaan_id' => $perusahaan_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
