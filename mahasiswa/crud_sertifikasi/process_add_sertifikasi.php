<?php
session_start();
include '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaSertifikasi = $_POST['nama_sertifikasi'];
    $lembagaId = $_POST['lembaga_id'];
    $nomorSk = $_POST['nomor_sk'];
    $tanggalDiperoleh = $_POST['tanggal_diperoleh'];
    $tanggalKadaluarsa = isset($_POST['tanggal_kadaluarsa']) ? $_POST['tanggal_kadaluarsa'] : NULL;
    $mahasiswaId = isset($_SESSION['mahasiswa_id']) ? $_SESSION['mahasiswa_id'] : null;

    if (!$mahasiswaId) {
        echo "Error: Mahasiswa ID is not set in session.";
        exit();
    }

    // Debugging output untuk memeriksa nilai `mahasiswa_id` dari session
    echo "Session Mahasiswa ID: $mahasiswaId<br>";

    // Validasi apakah `mahasiswa_id` ada di tabel `mahasiswas`
    $stmt = $pdo->prepare("SELECT id FROM mahasiswas WHERE id = :mahasiswaId");
    $stmt->bindParam(':mahasiswaId', $mahasiswaId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo "Error: Mahasiswa ID does not exist.";
        exit();
    }

    $bukti = [];
    $targetDir = "../../assets/mahasiswa/sertifikasi/";

    if (!empty($_FILES['bukti_file']['tmp_name'])) {
        $fileName = basename($_FILES['bukti_file']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $fileExt;
        $targetFilePath = $targetDir . $uniqueName;

        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $targetFilePath)) {
            $bukti[] = $targetFilePath;
        } else {
            echo "Gagal memindahkan file yang diunggah.";
            exit();
        }
    }

    if (!empty($_POST['bukti_link']) && $_POST['bukti_link'] !== '-') {
        $bukti[] = $_POST['bukti_link'];
    }

    $jsonBukti = json_encode($bukti);
    $stmt = $pdo->prepare("INSERT INTO sertifikasi (nama_sertifikasi, lembaga_id, nomor_sk, tanggal_diperoleh, tanggal_kadaluarsa, bukti, mahasiswa_id) 
                            VALUES (:namaSertifikasi, :lembagaId, :nomorSk, :tanggalDiperoleh, :tanggalKadaluarsa, :bukti, :mahasiswaId)");
    $stmt->bindParam(':namaSertifikasi', $namaSertifikasi);
    $stmt->bindParam(':lembagaId', $lembagaId);
    $stmt->bindParam(':nomorSk', $nomorSk);
    $stmt->bindParam(':tanggalDiperoleh', $tanggalDiperoleh);
    $stmt->bindParam(':tanggalKadaluarsa', $tanggalKadaluarsa);
    $stmt->bindParam(':bukti', $jsonBukti);
    $stmt->bindParam(':mahasiswaId', $mahasiswaId, PDO::PARAM_INT);

    echo "Nama Sertifikasi: $namaSertifikasi<br>";
    echo "Lembaga ID: $lembagaId<br>";
    echo "Nomor SK: $nomorSk<br>";
    echo "Tanggal Diperoleh: $tanggalDiperoleh<br>";
    echo "Tanggal Kadaluarsa: $tanggalKadaluarsa<br>";
    echo "Mahasiswa ID: $mahasiswaId<br>";
    echo "Bukti: $jsonBukti<br>";

    try {
        $stmt->execute();
        $_SESSION['success_message'] = "Sertifikasi berhasil ditambahkan.";
        header("Location: tampil_sertifikasi.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $stmt = null;
}

$pdo = null;
?>
