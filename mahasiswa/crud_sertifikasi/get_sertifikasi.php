<?php
session_start();
include '../../includes/db.php';

// Pastikan request adalah POST dan terdapat ID yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id']; // Ambil ID dari POST data

    // Query untuk mengambil data sertifikasi berdasarkan ID
    $stmt = $pdo->prepare("SELECT * FROM sertifikasi WHERE id = ?");
    $stmt->execute([$id]);
    $sertifikasi = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data ditemukan, kirimkan sebagai JSON
    if ($sertifikasi) {
        echo json_encode($sertifikasi);
    } else {
        // Jika data tidak ditemukan, kirimkan respons kode status 404
        http_response_code(404);
        echo json_encode(array("message" => "Data sertifikasi tidak ditemukan."));
    }
} else {
    // Jika tidak ada ID yang diterima atau request bukan POST, kirimkan respons kode status 400
    http_response_code(400);
    echo json_encode(array("message" => "Permintaan tidak valid."));
}
?>
