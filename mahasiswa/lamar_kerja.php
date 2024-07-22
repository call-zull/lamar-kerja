<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $lowongan_id = $_POST['lowongan_id'];
    $pesan = $_POST['pesan'];
    $ijazah = $_POST['transkip_ijazah'];
    $resume = $_POST['cv'];
    $khs = $_POST['khs'];
    $status = 'Pending';
    $salary = NULL;

    // Retrieve user_id from session
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        $_SESSION['error_message'] = "No user_id found in session.";
        header('Location: cari_kerja.php');
        exit();
    }

    // Get mahasiswa_id using user_id
    $getMahasiswaIdSql = "SELECT id FROM mahasiswas WHERE user_id = :user_id";
    $getMahasiswaIdStmt = $pdo->prepare($getMahasiswaIdSql);
    $getMahasiswaIdStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $getMahasiswaIdStmt->execute();
    $mahasiswa = $getMahasiswaIdStmt->fetch(PDO::FETCH_ASSOC);

    if ($mahasiswa) {
        $mahasiswa_id = $mahasiswa['id'];
    } else {
        $_SESSION['error_message'] = "Mahasiswa with user_id $user_id not found.";
        header('Location: cari_kerja.php');
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
    $sql = "INSERT INTO lamaran_mahasiswas 
    (lowongan_id, mahasiswa_id, pesan, status, salary, ijazah, resume, khs_semester_1, khs_semester_2, khs_semester_3, khs_semester_4, khs_semester_5, khs_semester_6, khs_semester_7, khs_semester_8) 
    VALUES 
    (:lowongan_id, :mahasiswa_id, :pesan, :status, :salary, :ijazah, :resume, :khs_semester_1, :khs_semester_2, :khs_semester_3, :khs_semester_4, :khs_semester_5, :khs_semester_6, :khs_semester_7, :khs_semester_8)";

    $stmt = $pdo->prepare($sql);

    // Split the KHS values into respective semesters
    $khs_values = explode(',', $khs);
    $khs_semester_1 = isset($khs_values[0]) ? $khs_values[0] : null;
    $khs_semester_2 = isset($khs_values[1]) ? $khs_values[1] : null;
    $khs_semester_3 = isset($khs_values[2]) ? $khs_values[2] : null;
    $khs_semester_4 = isset($khs_values[3]) ? $khs_values[3] : null;
    $khs_semester_5 = isset($khs_values[4]) ? $khs_values[4] : null;
    $khs_semester_6 = isset($khs_values[5]) ? $khs_values[5] : null;
    $khs_semester_7 = isset($khs_values[6]) ? $khs_values[6] : null;
    $khs_semester_8 = isset($khs_values[7]) ? $khs_values[7] : null;

    // Bind parameters
    $stmt->bindParam(':lowongan_id', $lowongan_id, PDO::PARAM_INT);
    $stmt->bindParam(':mahasiswa_id', $mahasiswa_id, PDO::PARAM_INT);
    $stmt->bindParam(':pesan', $pesan, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':salary', $salary, PDO::PARAM_STR);
    $stmt->bindParam(':ijazah', $ijazah, PDO::PARAM_STR);
    $stmt->bindParam(':resume', $resume, PDO::PARAM_STR);
    $stmt->bindParam(':khs_semester_1', $khs_semester_1, PDO::PARAM_STR);
    $stmt->bindParam(':khs_semester_2', $khs_semester_2, PDO::PARAM_STR);
    $stmt->bindParam(':khs_semester_3', $khs_semester_3, PDO::PARAM_STR);
    $stmt->bindParam(':khs_semester_4', $khs_semester_4, PDO::PARAM_STR);
    $stmt->bindParam(':khs_semester_5', $khs_semester_5, PDO::PARAM_STR);
    $stmt->bindParam(':khs_semester_6', $khs_semester_6, PDO::PARAM_STR);
    $stmt->bindParam(':khs_semester_7', $khs_semester_7, PDO::PARAM_STR);
    $stmt->bindParam(':khs_semester_8', $khs_semester_8, PDO::PARAM_STR);

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
