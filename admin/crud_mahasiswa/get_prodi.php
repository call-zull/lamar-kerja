<?php
include '../../includes/db.php'; 

if (isset($_POST['jurusan_id'])) {
    $jurusan_id = $_POST['jurusan_id'];
    $query = "SELECT id, nama_prodi FROM prodis WHERE jurusan_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$jurusan_id]);
    $prodis = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($prodis);
} else {
    echo json_encode(["error" => "jurusan_id not set"]);
}
?>
