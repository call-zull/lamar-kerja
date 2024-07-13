<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idPelatihan = $_POST['id'];
    $mahasiswa_id = $_SESSION['mahasiswa_id']; // Ambil mahasiswa_id dari sesi

    try {
        $stmt = $pdo->prepare("SELECT * FROM pelatihan WHERE id_pelatihan = :id AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':id', $idPelatihan, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Data tidak ditemukan.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Permintaan tidak valid.']);
}
?>
