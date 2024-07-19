<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['edit_id'];
    $edit_nama_pekerjaan = $_POST['edit_nama_pekerjaan'];
    $edit_posisi = $_POST['edit_posisi'];
    $edit_kualifikasi = $_POST['edit_kualifikasi'];
    $edit_prodi = json_decode($_POST['edit_prodi'], true); // Decode JSON into PHP array
    $edit_keahlian = json_decode($_POST['edit_keahlian'], true);
    $edit_batas_waktu = $_POST['edit_batas_waktu'];
    // $nama_pelamar = $_POST['edit_nama_pelamar'];
    // $status = $_POST['edit_status'];

    try {
        // $sql = "UPDATE lamaran_mahasiswas SET status = :status WHERE id = :id";
        $sql = "UPDATE lowongan_kerja SET 
        nama_pekerjaan = :nama_pekerjaan,
        posisi = :posisi,
        kualifikasi = :kualifikasi,
        prodi = :prodi,
        keahlian = :keahlian,
        batas_waktu = :batas_waktu
        WHERE id = :id";

        $stmt = $pdo->prepare($sql);
         // Bind parameters
        $stmt->bindParam(':nama_pekerjaan', $edit_nama_pekerjaan, PDO::PARAM_STR);
        $stmt->bindParam(':posisi', $edit_posisi, PDO::PARAM_STR);
        $stmt->bindParam(':kualifikasi', $edit_kualifikasi, PDO::PARAM_STR);
        $stmt->bindParam(':prodi', $_POST['edit_prodi'], PDO::PARAM_STR);
        $stmt->bindParam(':keahlian', $_POST['edit_keahlian'], PDO::PARAM_STR);
        $stmt->bindParam(':batas_waktu', $edit_batas_waktu, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the update statement
        $stmt->execute();

        $_SESSION['message'] = 'Data pelamar berhasil diperbarui.';
        header('Location: lowongan_kerja.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
