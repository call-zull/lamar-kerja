<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['edit_id'];
    $nama_pekerjaan = $_POST['edit_nama_pekerjaan'];
    $posisi = $_POST['edit_posisi'];
    $kualifikasi = $_POST['edit_kualifikasi'];
    $prodi = isset($_POST['edit_prodi']) ? $_POST['edit_prodi'] : '';
    $keahlian = $_POST['edit_keahlian'];
    $batas_waktu = $_POST['edit_batas_waktu']; 

    try {
        $sql = "UPDATE lowongan_kerja 
                SET nama_pekerjaan = :nama_pekerjaan, posisi = :posisi, kualifikasi = :kualifikasi, prodi = :prodi, keahlian = :keahlian, batas_waktu = :batas_waktu 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nama_pekerjaan' => $nama_pekerjaan,
            ':posisi' => $posisi,
            ':kualifikasi' => $kualifikasi,
            ':prodi' => $prodi,
            ':keahlian' => $keahlian,
            ':batas_waktu' => $batas_waktu, 
            ':id' => $id
        ]);

        $_SESSION['success_message'] = "Lowongan berhasil diupdate.";
        header('Location: lowongan_kerja.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

?>
