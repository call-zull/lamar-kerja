<?php
session_start();
include '../../includes/db.php'; // Pastikan file db.php sudah termasuk koneksi PDO

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idLomba = $_POST['id'];
    $namaLomba = $_POST['nama_lomba'];
    $idKategori = $_POST['id_kategori'];
    $idTingkatan = $_POST['id_tingkatan'];
    $prestasi = $_POST['prestasi'];
    $penyelenggara = $_POST['penyelenggara'];
    $tanggalPelaksanaan = $_POST['tanggal_pelaksanaan'];
    $tempatPelaksanaan = $_POST['tempat_pelaksanaan'];
    $mahasiswa_id = $_SESSION['mahasiswa_id']; // Ambil mahasiswa_id dari sesi

    // Proses upload bukti
    $bukti = [];

    // Jika ada file bukti yang diunggah, lakukan proses upload
    if (!empty($_FILES['bukti_file']['tmp_name'])) {
        $targetDir = "../../assets/mahasiswa/lomba/";

        $fileName = basename($_FILES['bukti_file']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $fileExt; // Buat nama file unik
        $targetFilePath = $targetDir . $uniqueName;

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $targetFilePath)) {
            $bukti[] = $targetFilePath;
        } else {
            $_SESSION['error_message'] = "Gagal mengunggah file bukti.";
            header("Location: tampil_lomba.php");
            exit();
        }
    }

    // Proses link Google Drive
    if (!empty($_POST['bukti_link']) && $_POST['bukti_link'] !== '-') {
        $bukti[] = $_POST['bukti_link'];
    }

    try {
        // Fetch existing lomba data if available
        $stmt = $pdo->prepare("SELECT bukti FROM lomba WHERE id = :id AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':id', $idLomba, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            $_SESSION['error_message'] = "Data lomba tidak ditemukan.";
            header("Location: tampil_lomba.php");
            exit();
        }

        $existingBukti = json_decode($result['bukti'], true);

        // If new bukti is uploaded or provided, replace the existing one
        if (!empty($bukti)) {
            $existingBukti = $bukti;
        }

        // Update data lomba di database menggunakan PDO
        $stmt = $pdo->prepare("UPDATE lomba SET nama_lomba = :namaLomba, id_kategori = :idKategori, 
                                id_tingkatan = :idTingkatan, prestasi = :prestasi, penyelenggara = :penyelenggara, 
                                tanggal_pelaksanaan = :tanggalPelaksanaan, tempat_pelaksanaan = :tempatPelaksanaan, 
                                bukti = :bukti WHERE id = :idLomba AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':namaLomba', $namaLomba);
        $stmt->bindParam(':idKategori', $idKategori);
        $stmt->bindParam(':idTingkatan', $idTingkatan);
        $stmt->bindParam(':prestasi', $prestasi);
        $stmt->bindParam(':penyelenggara', $penyelenggara);
        $stmt->bindParam(':tanggalPelaksanaan', $tanggalPelaksanaan);
        $stmt->bindParam(':tempatPelaksanaan', $tempatPelaksanaan);
        $stmt->bindParam(':bukti', json_encode($existingBukti)); // Simpan array bukti dalam format JSON
        $stmt->bindParam(':idLomba', $idLomba, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
        
        // Eksekusi statement
        if ($stmt->execute()) {
            // Jika berhasil disimpan, redirect ke halaman tampil_lomba.php
            $_SESSION['success_message'] = "Data lomba berhasil diperbarui.";
            header("Location: tampil_lomba.php");
            exit();
        } else {
            // Jika gagal disimpan, set pesan error
            $_SESSION['error_message'] = "Gagal memperbarui data lomba.";
            header("Location: tampil_lomba.php");
            exit();
        }
        
        // Tutup statement
        $stmt = null; // Tutup statement dengan mengosongkan objek
        
    } catch (PDOException $e) {
        // Tangani kesalahan PDO
        $_SESSION['error_message'] = "Terjadi kesalahan saat memperbarui data lomba: " . $e->getMessage();
        header("Location: tampil_lomba.php");
        exit();
    }
}

// Tutup koneksi database (jika perlu, tergantung implementasi db.php)
// $pdo = null;
?>
