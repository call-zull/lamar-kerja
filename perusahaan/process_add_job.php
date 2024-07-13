<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pekerjaan = $_POST['nama_pekerjaan'];
    $posisi = $_POST['posisi'];
    $kualifikasi = $_POST['kualifikasi'];
    $prodi_id = isset($_POST['prodi_id']) && !empty($_POST['prodi_id']) ? $_POST['prodi_id'] : NULL;
    $keahlian = $_POST['keahlian'];

    try {
        $sql = "INSERT INTO lowongan_kerja (nama_pekerjaan, posisi, kualifikasi, prodi_id, keahlian) 
                VALUES (:nama_pekerjaan, :posisi, :kualifikasi, :prodi_id, :keahlian)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama_pekerjaan' => $nama_pekerjaan,
            ':posisi' => $posisi,
            ':kualifikasi' => $kualifikasi,
            ':prodi_id' => $prodi_id,
            ':keahlian' => $keahlian
        ]);

        header('Location: lowongan_kerja.php?success=1');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
