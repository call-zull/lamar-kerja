<?php
session_start();
include '../../includes/db.php'; // Pastikan file db.php sudah termasuk koneksi PDO

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProyek = $_POST['id'];
    $namaProyek = $_POST['nama_proyek'];
    $partner = $_POST['partner'];
    $peran = $_POST['peran'];
    $waktuAwal = $_POST['waktu_awal'];
    $waktuSelesai = $_POST['waktu_selesai'];
    $tujuanProyek = $_POST['tujuan_proyek'];
    $mahasiswa_id = $_SESSION['mahasiswa_id']; // Ambil mahasiswa_id dari sesi

    // Proses link Google Drive
    $bukti = [];

    // Jika ada file bukti yang diunggah, lakukan proses upload
    if (!empty($_FILES['bukti_file']['tmp_name'])) {
        $targetDir = "../../assets/mahasiswa/proyek/";

        $fileName = basename($_FILES['bukti_file']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $fileExt; // Buat nama file unik
        $targetFilePath = $targetDir . $uniqueName;

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $targetFilePath)) {
            $bukti[] = $targetFilePath;
        } else {
            $_SESSION['error_message'] = "Gagal mengunggah file bukti.";
            header("Location: tampil_proyek.php");
            exit();
        }
    }

    // Proses link Google Drive
    if (!empty($_POST['bukti_link']) && $_POST['bukti_link'] !== '-') {
        $bukti[] = $_POST['bukti_link'];
    }

    try {
        // Fetch existing proyek data
        $stmt = $pdo->prepare("SELECT bukti FROM proyek WHERE id = :idProyek AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':idProyek', $idProyek, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $_SESSION['error_message'] = "Data proyek tidak ditemukan.";
            header("Location: tampil_proyek.php");
            exit();
        }

        $existingBukti = json_decode($result['bukti'], true);

        // Hapus bukti jika ada perintah untuk menghapus
        if (isset($_POST['delete_bukti'])) {
            $index = $_POST['delete_bukti'];
            if (isset($existingBukti[$index])) {
                unlink($existingBukti[$index]); // Hapus file fisik dari server
                unset($existingBukti[$index]); // Hapus referensi dari array bukti
                $existingBukti = array_values($existingBukti); // Reset kembali index array
            }
        }

        // Jika ada bukti baru yang diunggah, gabungkan dengan bukti yang ada
        if (!empty($bukti)) {
            $existingBukti = array_merge($existingBukti, $bukti);
        }

        // Update data proyek di database menggunakan prepared statement PDO
        $stmt = $pdo->prepare("UPDATE proyek SET nama_proyek = :namaProyek, partner = :partner, 
                                peran = :peran, waktu_awal = :waktuAwal, waktu_selesai = :waktuSelesai, 
                                tujuan_proyek = :tujuanProyek, bukti = :bukti 
                                WHERE id = :idProyek AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':namaProyek', $namaProyek);
        $stmt->bindParam(':partner', $partner);
        $stmt->bindParam(':peran', $peran);
        $stmt->bindParam(':waktuAwal', $waktuAwal);
        $stmt->bindParam(':waktuSelesai', $waktuSelesai);
        $stmt->bindParam(':tujuanProyek', $tujuanProyek);
        $stmt->bindParam(':bukti', json_encode($existingBukti)); // Simpan array bukti dalam format JSON
        $stmt->bindParam(':idProyek', $idProyek, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);

        // Eksekusi statement
        if ($stmt->execute()) {
            // Jika berhasil disimpan, redirect ke halaman tampil_proyek.php
            $_SESSION['success_message'] = "Data proyek berhasil diperbarui.";
            header("Location: tampil_proyek.php");
            exit();
        } else {
            // Jika gagal disimpan, set pesan error
            $_SESSION['error_message'] = "Gagal memperbarui data proyek.";
            header("Location: tampil_proyek.php");
            exit();
        }

    } catch (PDOException $e) {
        // Tangani kesalahan PDO
        $_SESSION['error_message'] = "Terjadi kesalahan saat memperbarui data proyek: " . $e->getMessage();
        header("Location: tampil_proyek.php");
        exit();
    }
}

// Tutup koneksi database (jika perlu, tergantung implementasi db.php)
// $pdo = null;
?>
