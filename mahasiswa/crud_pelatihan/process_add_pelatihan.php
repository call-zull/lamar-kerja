<?php
session_start();
include '../../includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPelatihan = $_POST['nama_pelatihan'];
    $materi = $_POST['materi'];
    $deskripsi = $_POST['deskripsi'];
    $tanggalMulai = $_POST['tanggal_mulai'];
    $tanggalSelesai = $_POST['tanggal_selesai'];
    $idTingkatan = $_POST['id_tingkatan'];
    $tempatPelaksanaan = $_POST['tempat_pelaksanaan'];
    $penyelenggara = $_POST['penyelenggara'];
    $mahasiswaId = $_SESSION['mahasiswa_id'];

    $bukti = [];
    $targetDir = "../../assets/mahasiswa/pelatihan/";

    // Proses upload file bukti
    if (!empty($_FILES['bukti_file']['tmp_name'])) {
        $fileName = basename($_FILES['bukti_file']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $fileExt; // Buat nama file unik
        $targetFilePath = $targetDir . $uniqueName;

        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $targetFilePath)) {
            $bukti[] = $targetFilePath;
        } else {
            echo "Gagal memindahkan file yang diunggah.";
        }
    }

    // Proses link Google Drive
    if (!empty($_POST['bukti_link']) && $_POST['bukti_link'] !== '-') {
        $bukti[] = $_POST['bukti_link'];
    }

    // Simpan data ke database menggunakan prepared statement PDO
    $jsonBukti = json_encode($bukti); // Simpan array path bukti sebagai JSON
    $stmt = $pdo->prepare("INSERT INTO pelatihan (nama_pelatihan, materi, deskripsi, tanggal_mulai, tanggal_selesai, id_tingkatan, tempat_pelaksanaan, penyelenggara, bukti, mahasiswa_id) 
                            VALUES (:namaPelatihan, :materi, :deskripsi, :tanggalMulai, :tanggalSelesai, :idTingkatan, :tempatPelaksanaan, :penyelenggara, :bukti, :mahasiswaId)");
    $stmt->bindParam(':namaPelatihan', $namaPelatihan);
    $stmt->bindParam(':materi', $materi);
    $stmt->bindParam(':deskripsi', $deskripsi);
    $stmt->bindParam(':tanggalMulai', $tanggalMulai);
    $stmt->bindParam(':tanggalSelesai', $tanggalSelesai);
    $stmt->bindParam(':idTingkatan', $idTingkatan);
    $stmt->bindParam(':tempatPelaksanaan', $tempatPelaksanaan);
    $stmt->bindParam(':penyelenggara', $penyelenggara);
    $stmt->bindParam(':bukti', $jsonBukti);
    $stmt->bindParam(':mahasiswaId', $mahasiswaId);

    // Eksekusi statement
    try {
        $stmt->execute();
        // Jika berhasil disimpan, redirect ke halaman tampil_pelatihan.php
        $_SESSION['success_message'] = "Pelatihan berhasil ditambahkan.";
        header("Location: tampil_pelatihan.php");
        exit();
    } catch (PDOException $e) {
        // Jika terjadi error, tampilkan pesan error
        echo "Error: " . $e->getMessage();
    }
    
    // Tutup statement
    $stmt = null;
}

// Tutup koneksi database
$pdo = null;
?>

