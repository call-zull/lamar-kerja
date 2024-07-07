<?php
session_start();
include '../../includes/db.php'; // Pastikan file db.php sudah termasuk koneksi PDO

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPelatihan = $_POST['id'];
    $namaPelatihan = $_POST['nama_pelatihan'];
    $materi = $_POST['materi'];
    $deskripsi = $_POST['deskripsi'];
    $tanggalMulai = $_POST['tanggal_mulai'];
    $tanggalSelesai = $_POST['tanggal_selesai'];
    $idTingkatan = $_POST['id_tingkatan'];
    $tempatPelaksanaan = $_POST['tempat_pelaksanaan'];
    $idPenyelenggara = $_POST['id_penyelenggara'];
    $mahasiswa_id = $_SESSION['mahasiswa_id']; // Ambil mahasiswa_id dari sesi

    // Proses upload bukti
    $bukti = [];

    // Jika ada file bukti yang diunggah, lakukan proses upload
    if (!empty($_FILES['bukti_file']['tmp_name'])) {
        $targetDir = "../../assets/mahasiswa/pelatihan/";

        $fileName = basename($_FILES['bukti_file']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $fileExt; // Buat nama file unik
        $targetFilePath = $targetDir . $uniqueName;

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $targetFilePath)) {
            $bukti[] = $targetFilePath;
        } else {
            $_SESSION['error_message'] = "Gagal mengunggah file bukti.";
            header("Location: tampil_pelatihan.php");
            exit();
        }
    }

    // Proses link Google Drive
    if (!empty($_POST['bukti_link']) && $_POST['bukti_link'] !== '-') {
        $bukti[] = $_POST['bukti_link'];
    }

    try {
        // Fetch existing pelatihan data if available
        $stmt = $pdo->prepare("SELECT bukti FROM pelatihan WHERE id_pelatihan = :id AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':id', $idPelatihan, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            $_SESSION['error_message'] = "Data pelatihan tidak ditemukan.";
            header("Location: tampil_pelatihan.php");
            exit();
        }

        $existingBukti = json_decode($result['bukti'], true);

        // If new bukti is uploaded or provided, add to the existing bukti array
        if (!empty($bukti)) {
            $existingBukti = array_merge($existingBukti, $bukti);
        }

        // Update data pelatihan di database menggunakan PDO
        $stmt = $pdo->prepare("UPDATE pelatihan SET nama_pelatihan = :namaPelatihan, materi = :materi, deskripsi = :deskripsi, 
                                tanggal_mulai = :tanggalMulai, tanggal_selesai = :tanggalSelesai, id_tingkatan = :idTingkatan, 
                                tempat_pelaksanaan = :tempatPelaksanaan, id_penyelenggara = :idPenyelenggara, bukti = :bukti 
                                WHERE id_pelatihan = :idPelatihan AND mahasiswa_id = :mahasiswa_id");
        $stmt->bindParam(':namaPelatihan', $namaPelatihan);
        $stmt->bindParam(':materi', $materi);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':tanggalMulai', $tanggalMulai);
        $stmt->bindParam(':tanggalSelesai', $tanggalSelesai);
        $stmt->bindParam(':idTingkatan', $idTingkatan);
        $stmt->bindParam(':tempatPelaksanaan', $tempatPelaksanaan);
        $stmt->bindParam(':idPenyelenggara', $idPenyelenggara);
        $stmt->bindParam(':bukti', json_encode($existingBukti)); // Simpan array bukti dalam format JSON
        $stmt->bindParam(':idPelatihan', $idPelatihan, PDO::PARAM_INT);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
        
        // Eksekusi statement
        if ($stmt->execute()) {
            // Jika berhasil disimpan, redirect ke halaman tampil_pelatihan.php
            $_SESSION['success_message'] = "Data pelatihan berhasil diperbarui.";
            header("Location: tampil_pelatihan.php");
            exit();
        } else {
            // Jika gagal disimpan, set pesan error
            $_SESSION['error_message'] = "Gagal memperbarui data pelatihan.";
            header("Location: tampil_pelatihan.php");
            exit();
        }
        
        // Tutup statement
        $stmt = null; // Tutup statement dengan mengosongkan objek
        
    } catch (PDOException $e) {
        // Tangani kesalahan PDO
        $_SESSION['error_message'] = "Terjadi kesalahan saat memperbarui data pelatihan: " . $e->getMessage();
        header("Location: tampil_pelatihan.php");
        exit();
    }
}

// Tutup koneksi database (jika perlu, tergantung implementasi db.php)
// $pdo = null;
?>
