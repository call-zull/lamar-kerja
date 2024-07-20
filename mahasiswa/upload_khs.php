<?php
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $semester = $_POST['semester'];
    $target_dir = "../assets/mahasiswa/khs/";
    $target_file = $target_dir . basename($_FILES["khs_file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size (5 MB limit)
    if ($_FILES["khs_file"]["size"] > 5000000) {
        $_SESSION['message'] = "Maaf, file KHS Semester $semester terlalu besar, maksimal 5 MB.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($fileType, ['pdf', 'jpg', 'jpeg', 'png'])) {
        $_SESSION['message'] = "Maaf, hanya bisa upload file KHS Semester $semester dengan format PDF, JPG, JPEG, dan PNG.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $_SESSION['message'] = "Maaf, file KHS Semester $semester belum diupload.";
        header('Location: kartu_hasil_studi.php');
        exit;
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["khs_file"]["tmp_name"], $target_file)) {
            $filename = basename($_FILES["khs_file"]["name"]);

            // Update database
            $sql = "UPDATE mahasiswas SET khs_semester_$semester = ? WHERE user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$filename, $user_id]);

            $_SESSION['message'] = "File KHS Semester $semester " . htmlspecialchars($filename) . " berhasil diupload.";
            header('Location: kartu_hasil_studi.php');
            exit;
        } else {
            $_SESSION['message'] = "Maaf, ada kesalahan dalam mengupload file KHS Semester $semester.";
            header('Location: kartu_hasil_studi.php');
            exit;
        }
    }
}
?>
