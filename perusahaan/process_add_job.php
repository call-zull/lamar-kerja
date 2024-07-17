<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Fungsi untuk menambahkan lowongan pekerjaan
function addJob($pdo, $nama_pekerjaan, $posisi, $kualifikasi, $prodi, $keahlian, $batas_waktu) {
    try {
        // Mulai transaksi
        $pdo->beginTransaction();
        
        // Mengubah data prodi dan keahlian menjadi JSON
        $prodi_json = json_encode($prodi);
        $keahlian_json = json_encode($keahlian);

        // Menambahkan lowongan pekerjaan ke tabel lowongan_kerja
        $sql = "INSERT INTO lowongan_kerja (nama_pekerjaan, posisi, kualifikasi, prodi, keahlian, tanggal_posting, batas_waktu) 
                VALUES (:nama_pekerjaan, :posisi, :kualifikasi, :prodi, :keahlian, NOW(), :batas_waktu)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nama_pekerjaan', $nama_pekerjaan);
        $stmt->bindParam(':posisi', $posisi);
        $stmt->bindParam(':kualifikasi', $kualifikasi);
        $stmt->bindParam(':prodi', $prodi_json);
        $stmt->bindParam(':keahlian', $keahlian_json);
        $stmt->bindParam(':batas_waktu', $batas_waktu);
        $stmt->execute();
        
        // Komit transaksi
        $pdo->commit();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $pdo->rollBack();
        echo "Error adding job: " . $e->getMessage();
        return false;
    }
}

// Memeriksa apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pekerjaan = $_POST['nama_pekerjaan'];
    $posisi = $_POST['posisi'];
    $kualifikasi = $_POST['kualifikasi'];
    $prodi = json_decode($_POST['prodi'], true); // Memastikan ini adalah array
    $keahlian = json_decode($_POST['keahlian'], true); // Mendekode string JSON
    $batas_waktu = $_POST['batas_waktu']; // Mendapatkan batas waktu dari input form

    // Menambahkan lowongan pekerjaan ke database
    if (addJob($pdo, $nama_pekerjaan, $posisi, $kualifikasi, $prodi, $keahlian, $batas_waktu)) {
        $_SESSION['success_message'] = "Lowongan berhasil ditambahkan.";
        header('Location: lowongan_kerja.php');
        exit;
    }
}
?>

?>
