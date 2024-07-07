<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    http_response_code(403);
    echo json_encode(array("message" => "Akses ditolak."));
    exit;
}

// Pastikan request adalah POST dan terdapat ID yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id']; // Ambil ID dari POST data
    $mahasiswa_id = $_SESSION['mahasiswa_id']; // Ambil mahasiswa_id dari sesi

    // Query untuk mengambil data pelatihan berdasarkan ID dan mahasiswa_id
    $stmt = $pdo->prepare("SELECT * FROM pelatihan WHERE id_pelatihan = ? AND mahasiswa_id = ?");
    $stmt->execute([$id, $mahasiswa_id]);
    $pelatihan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data ditemukan, kirimkan sebagai JSON
    if ($pelatihan) {
        echo json_encode($pelatihan);
    } else {
        // Jika data tidak ditemukan, kirimkan respons kode status 404
        http_response_code(404);
        echo json_encode(array("message" => "Data pelatihan tidak ditemukan."));
    }
} else {
    // Jika tidak ada ID yang diterima atau request bukan POST, kirimkan respons kode status 400
    http_response_code(400);
    echo json_encode(array("message" => "Permintaan tidak valid."));
}
?>
