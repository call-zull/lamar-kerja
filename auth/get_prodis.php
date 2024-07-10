<?php
include('../includes/db.php');

if (isset($_POST['jurusan_id'])) {
    $jurusan_id = $_POST['jurusan_id'];
    $stmt = $pdo->prepare("SELECT id, nama_prodi FROM prodis WHERE jurusan_id = :jurusan_id");
    $stmt->execute(['jurusan_id' => $jurusan_id]);
    
    echo '<option value="" disabled selected>Pilih prodi</option>';
    while ($row = $stmt->fetch()) {
        echo '<option value="' . $row['id'] . '">' . $row['nama_prodi'] . '</option>';
    }
}
?>
