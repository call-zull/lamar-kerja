<?php
include('../includes/db.php');

if (isset($_POST['jurusan_id'])) {
    $jurusan_id = $_POST['jurusan_id'];
    $stmt = $pdo->prepare("SELECT id, nama_prodi FROM prodis WHERE jurusan_id = ?");
    $stmt->execute([$jurusan_id]);
    $prodiOptions = '<option value="" disabled selected>Pilih Prodi</option>';
    while ($row = $stmt->fetch()) {
        $prodiOptions .= '<option value="' . $row['id'] . '">' . $row['nama_prodi'] . '</option>';
    }
    echo $prodiOptions;
}
?>
