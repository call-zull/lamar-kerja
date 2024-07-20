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
    $target_dir = "../assets/mahasiswa/resume/";
    $target_file = $target_dir . basename($_FILES["resume"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size (5 MB limit)
    if ($_FILES["resume"]["size"] > 5000000) {
        $_SESSION['message'] = "Maaf, file Anda terlalu besar, maksimal 5 MB.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($fileType, ['pdf', 'jpg', 'jpeg', 'png'])) {
        $_SESSION['message'] = "Maaf, hanya bisa upload file dengan format PDF, JPG, JPEG, dan PNG.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $_SESSION['message'] = "Maaf, file Anda belum diupload.";
        header('Location: resume.php');
        exit;
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
            $filename = basename($_FILES["resume"]["name"]);

            // Update database
            $sql = "UPDATE mahasiswas SET resume = ? WHERE user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$filename, $user_id]);

            $_SESSION['message'] = "File ". htmlspecialchars($filename). " berhasil diupload.";
            header('Location: resume.php');
            exit;
        } else {
            $_SESSION['message'] = "Maaf, ada kesalahan dalam mengupload file.";
            header('Location: resume.php');
            exit;
        }
    }
}
?>
