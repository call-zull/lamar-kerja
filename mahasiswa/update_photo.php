<?php
session_start();

// Cek apakah pengguna login sebagai mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Sertakan koneksi database
include '../includes/db.php';

// Cek apakah ada file yang diunggah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $user_id = $_SESSION['user_id'];
    $target_dir = "../assets/mahasiswa/profile/";
    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $uploadOk = 1;

    // Cek apakah file adalah gambar
    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<script>alert('File is not an image.'); window.location.href = 'previous_page.php';</script>";
        $uploadOk = 0;
    }

    // Cek ukuran file
    if ($_FILES["profile_image"]["size"] > 500000) {
        echo "<script>alert('Sorry, your file is too large.'); window.location.href = 'previous_page.php';</script>";
        $uploadOk = 0;
    }

    // Izinkan format file tertentu
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.'); window.location.href = 'previous_page.php';</script>";
        $uploadOk = 0;
    }

    // Cek apakah uploadOk disetel ke 0 oleh kesalahan
    if ($uploadOk == 0) {
        echo "<script>alert('Sorry, your file was not uploaded.'); window.location.href = 'previous_page.php';</script>";
    } else {
        // Coba unggah file
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            // Update gambar profil di database
            $sql = "UPDATE mahasiswas SET profile_image = ? WHERE user_id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([basename($_FILES["profile_image"]["name"]), $user_id])) {
                echo "<script>alert('Profile image updated successfully'); window.location.href = 'profile_mahasiswa.php';</script>";
            } else {
                echo "<script>alert('Sorry, there was an error updating your profile image.'); window.location.href = 'previous_page.php';</script>";
            }
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.'); window.location.href = 'previous_page.php';</script>";
        }
    }
} else {
    echo "<script>alert('No file uploaded or invalid request.'); window.location.href = 'previous_page.php';</script>";
}

?>