<?php
session_start();
include '../../includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaLomba = $_POST['nama_lomba'];
    $idKategori = $_POST['id_kategori'];
    $idTingkatan = $_POST['id_tingkatan'];
    $prestasi = $_POST['prestasi'];
    $penyelenggara = $_POST['penyelenggara'];
    $tanggalPelaksanaan = $_POST['tanggal_pelaksanaan'];
    $tempatPelaksanaan = $_POST['tempat_pelaksanaan'];
    $mahasiswaId = $_SESSION['mahasiswa_id'];

    $bukti = [];
    $targetDir = "../../assets/mahasiswa/lomba/";

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
    $stmt = $pdo->prepare("INSERT INTO lomba (nama_lomba, id_kategori, id_tingkatan, prestasi, penyelenggara, tanggal_pelaksanaan, tempat_pelaksanaan, bukti, mahasiswa_id) 
                            VALUES (:namaLomba, :idKategori, :idTingkatan, :prestasi, :penyelenggara, :tanggalPelaksanaan, :tempatPelaksanaan, :bukti, :mahasiswaId)");
    $stmt->bindParam(':namaLomba', $namaLomba);
    $stmt->bindParam(':idKategori', $idKategori);
    $stmt->bindParam(':idTingkatan', $idTingkatan);
    $stmt->bindParam(':prestasi', $prestasi);
    $stmt->bindParam(':penyelenggara', $penyelenggara);
    $stmt->bindParam(':tanggalPelaksanaan', $tanggalPelaksanaan);
    $stmt->bindParam(':tempatPelaksanaan', $tempatPelaksanaan);
    $stmt->bindParam(':bukti', $jsonBukti);
    $stmt->bindParam(':mahasiswaId', $mahasiswaId);

    // Eksekusi statement
    try {
        $stmt->execute();
        // Jika berhasil disimpan, redirect ke halaman tampil_lomba.php
        $_SESSION['success_message'] = "Lomba berhasil ditambahkan.";
        header("Location: tampil_lomba.php");
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
