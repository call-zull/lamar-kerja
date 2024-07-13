<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['edit_id'];
    $nama_pekerjaan = $_POST['edit_nama_pekerjaan'];
    $posisi = $_POST['edit_posisi'];
    $kualifikasi = $_POST['edit_kualifikasi'];
    $prodi_id = isset($_POST['edit_prodi_id']) && !empty($_POST['edit_prodi_id']) ? $_POST['edit_prodi_id'] : NULL;
    $keahlian = $_POST['edit_keahlian'];

    try {
        $sql = "UPDATE lowongan_kerja 
                SET nama_pekerjaan = :nama_pekerjaan, posisi = :posisi, kualifikasi = :kualifikasi, prodi_id = :prodi_id, keahlian = :keahlian 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama_pekerjaan' => $nama_pekerjaan,
            ':posisi' => $posisi,
            ':kualifikasi' => $kualifikasi,
            ':prodi_id' => $prodi_id,
            ':keahlian' => $keahlian,
            ':id' => $id
        ]);

        header('Location: lowongan_kerja.php?success=1');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
