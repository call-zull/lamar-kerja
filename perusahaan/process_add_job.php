<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Fungsi untuk menambahkan lowongan pekerjaan
function addJob($pdo, $nama_pekerjaan, $posisi, $kualifikasi, $prodi, $keahlian, $batas_waktu, $range_gajih, $jenis_kerja, $sistem_kerja, $jobdesk, $perusahaan_id)
{
    try {
        // Mulai transaksi
        $pdo->beginTransaction();

        // Mengubah data prodi dan keahlian menjadi JSON
        $prodi_json = json_encode($prodi);
        $keahlian_json = json_encode($keahlian);

        // Menambahkan lowongan pekerjaan ke tabel lowongan_kerja
        $sql = "INSERT INTO lowongan_kerja (nama_pekerjaan, posisi, kualifikasi, prodi, keahlian, tanggal_posting, batas_waktu, range_gajih, jenis_kerja, sistem_kerja, jobdesk, perusahaan_id) 
                VALUES (:nama_pekerjaan, :posisi, :kualifikasi, :prodi, :keahlian, NOW(), :batas_waktu, :range_gajih, :jenis_kerja, :sistem_kerja, :jobdesk, :perusahaan_id)";
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
        $stmt->execute();

        // Komit transaksi
        $pdo->commit();

        return true;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $pdo->rollBack();
        // Simpan pesan kesalahan ke sesi
        $_SESSION['error_message'] = "Error adding job: " . $e->getMessage();
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
    $nama_pekerjaan = htmlspecialchars($_POST['nama_pekerjaan']);
    $posisi = htmlspecialchars($_POST['posisi']);
    $kualifikasi = htmlspecialchars($_POST['kualifikasi']);
    $prodi = json_decode($_POST['prodi'], true); // Memastikan ini adalah array
    $keahlian = json_decode($_POST['keahlian'], true); // Mendekode string JSON
    $batas_waktu = $_POST['batas_waktu'];
    $range_gajih = htmlspecialchars($_POST['range_gajih']);
    $jenis_kerja = $_POST['jenis_kerja'];
    $sistem_kerja = $_POST['sistem_kerja'];
    $jobdesk = htmlspecialchars($_POST['jobdesk']);
    $user_id = $_SESSION['user_id']; // Assuming the user_id is the logged-in user's ID

    // Memeriksa apakah perusahaan_id ada di tabel perusahaans
    $perusahaanInfo = getPerusahaanInfo($pdo, $user_id);
    if (!$perusahaanInfo) {
        $_SESSION['error_message'] = "Perusahaan tidak ditemukan.";
        header('Location: lowongan_kerja.php');
        exit;
    }

    $perusahaan_id = $perusahaanInfo['id'];

    // Menambahkan lowongan pekerjaan ke database
    if (addJob($pdo, $nama_pekerjaan, $posisi, $kualifikasi, $prodi, $keahlian, $batas_waktu, $range_gajih, $jenis_kerja, $sistem_kerja, $jobdesk, $perusahaan_id)) {
        $_SESSION['success_message'] = "Lowongan berhasil ditambahkan.";
        header('Location: lowongan_kerja.php');
        exit;
    } else {
        header('Location: lowongan_kerja.php');
        exit;
    }
}
