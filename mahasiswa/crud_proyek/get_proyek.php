<?php
session_start();
include '../../includes/db.php'; // Ensure db.php is included for PDO connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idProyek = $_POST['id'];
    $mahasiswa_id = $_SESSION['mahasiswa_id'];

    $sql = "SELECT id, nama_proyek, partner, peran, waktu_awal, waktu_selesai, tujuan_proyek, bukti 
            FROM proyek 
            WHERE id = :idProyek AND mahasiswa_id = :mahasiswa_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idProyek', $idProyek, PDO::PARAM_INT);
    $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Data proyek tidak ditemukan.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
?>
