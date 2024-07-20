<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $lowongan_id = $_POST['lowongan_id'];
    $pesan = $_POST['pesan'];
    $transkip_ijazah = $_POST['transkip_ijazah'];
    $cv = $_POST['cv'];
    $khs = $_POST['khs'];
    $status = 'Pending';

    // Retrieve mahasiswa_id from session
    if (isset($_SESSION['user_id'])) {
        $mahasiswa_id = $_SESSION['user_id'];
    } else {
        echo "Error: No mahasiswa_id found in session.";
        exit();
    }

    // Check if mahasiswa_id exists in the mahasiswas table
    $checkSql = "SELECT COUNT(*) FROM mahasiswas WHERE user_id = :mahasiswa_id";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count == 0) {
        echo "Error: mahasiswa_id $mahasiswa_id does not exist in the mahasiswas table.";
        exit();
    }

    // Check if the student has already applied for the job
    $checkApplicationSql = "SELECT COUNT(*) FROM lamaran_mahasiswas WHERE lowongan_id = :lowongan_id AND mahasiswa_id = :mahasiswa_id AND status = 'Pending'";
    $checkApplicationStmt = $pdo->prepare($checkApplicationSql);
    $checkApplicationStmt->bindParam(':lowongan_id', $lowongan_id, PDO::PARAM_INT);
    $checkApplicationStmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
    $checkApplicationStmt->execute();
    $applicationCount = $checkApplicationStmt->fetchColumn();

    if ($applicationCount > 0) {
        $_SESSION['error_message'] = "Anda sudah melamar untuk lowongan ini dan masih menunggu balasan.";
        header('Location: cari_kerja.php');
        exit();
    }

    // Prepare and bind
    $sql = "INSERT INTO lamaran_mahasiswas (lowongan_id, mahasiswa_id, pesan, status, transkip_ijazah, cv, khs) VALUES (:lowongan_id, :mahasiswa_id, :pesan, :status, :transkip_ijazah, :cv, :khs)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':lowongan_id', $lowongan_id, PDO::PARAM_INT);
    $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
    $stmt->bindParam(':pesan', $pesan, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':transkip_ijazah', $transkip_ijazah, PDO::PARAM_STR);
    $stmt->bindParam(':cv', $cv, PDO::PARAM_STR);
    $stmt->bindParam(':khs', $khs, PDO::PARAM_STR);

    // Execute the statement
    try {
        $stmt->execute();
        $_SESSION['success_message'] = "Lamaran berhasil dikirim.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }

    header('Location: cari_kerja.php');
    exit;
}
?>
