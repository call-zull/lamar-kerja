<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Fungsi untuk memperbarui lowongan pekerjaan
function updateJob($pdo, $id, $nama_pekerjaan, $posisi, $kualifikasi, $prodi, $keahlian, $batas_waktu, $range_gajih, $jenis_kerja, $sistem_kerja, $jobdesk, $perusahaan_id)
{
    try {
        // Mulai transaksi
        $pdo->beginTransaction();

        // Mengubah data prodi dan keahlian menjadi JSON
        $prodi_json = json_encode($prodi);
        $keahlian_json = json_encode($keahlian);

        // Memperbarui lowongan pekerjaan di tabel lowongan_kerja
        $sql = "UPDATE lowongan_kerja SET 
            nama_pekerjaan = :nama_pekerjaan, 
            posisi = :posisi, 
            kualifikasi = :kualifikasi, 
            prodi = :prodi, 
            keahlian = :keahlian, 
            batas_waktu = :batas_waktu, 
            range_gajih = :range_gajih, 
            jenis_kerja = :jenis_kerja, 
            sistem_kerja = :sistem_kerja, 
            jobdesk = :jobdesk, 
            perusahaan_id = :perusahaan_id
            WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nama_pekerjaan', $nama_pekerjaan);
        $stmt->bindParam(':posisi', $posisi);
        $stmt->bindParam(':kualifikasi', $kualifikasi);
        $stmt->bindParam(':prodi', $prodi_json);
        $stmt->bindParam(':keahlian', $keahlian_json);
        $stmt->bindParam(':batas_waktu', $batas_waktu);
        $stmt->bindParam(':range_gajih', $range_gajih);
        $stmt->bindParam(':jenis_kerja', $jenis_kerja);
        $stmt->bindParam(':sistem_kerja', $sistem_kerja);
        $stmt->bindParam(':jobdesk', $jobdesk);
        $stmt->bindParam(':perusahaan_id', $perusahaan_id);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Komit transaksi
        $pdo->commit();

        return true;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $pdo->rollBack();
        // Simpan pesan kesalahan ke sesi
        $_SESSION['error_message'] = "Error updating job: " . $e->getMessage();
        return false;
    }
}

// Memeriksa apakah perusahaan_id ada di tabel perusahaans dan mendapatkan informasi perusahaan
function getPerusahaanInfo($pdo, $user_id)
{
    $sql = "SELECT id FROM perusahaans WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Memeriksa apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['edit_id'];
    $nama_pekerjaan = htmlspecialchars($_POST['edit_nama_pekerjaan']);
    $posisi = htmlspecialchars($_POST['edit_posisi']);
    $kualifikasi = htmlspecialchars($_POST['edit_kualifikasi']);
    $prodi = json_decode($_POST['edit_prodi'], true); // Memastikan ini adalah array
    $keahlian = json_decode($_POST['edit_keahlian'], true); // Mendekode string JSON
    $batas_waktu = $_POST['edit_batas_waktu'];
    $range_gajih = htmlspecialchars($_POST['edit_range_gajih']);
    $jenis_kerja = $_POST['edit_jenis_kerja'];
    $sistem_kerja = $_POST['edit_sistem_kerja'];
    $jobdesk = htmlspecialchars($_POST['edit_jobdesk']);
    $user_id = $_SESSION['user_id']; // Assuming the user_id is the logged-in user's ID

    // Memeriksa apakah perusahaan_id ada di tabel perusahaans
    $perusahaanInfo = getPerusahaanInfo($pdo, $user_id);
    if (!$perusahaanInfo) {
        $_SESSION['error_message'] = "Perusahaan tidak ditemukan.";
        header('Location: lowongan_kerja.php');
        exit;
    }

    $perusahaan_id = $perusahaanInfo['id'];

    // Memperbarui lowongan pekerjaan di database
    if (updateJob($pdo, $id, $nama_pekerjaan, $posisi, $kualifikasi, $prodi, $keahlian, $batas_waktu, $range_gajih, $jenis_kerja, $sistem_kerja, $jobdesk, $perusahaan_id)) {
        $_SESSION['success_message'] = "Lowongan berhasil diperbarui.";
        header('Location: lowongan_kerja.php');
        exit;
    } else {
        header('Location: lowongan_kerja.php');
        exit;
    }
}
?>