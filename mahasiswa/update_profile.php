<?php
session_start();

// Check if user is authenticated as mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mahasiswa_id = $_POST['id'];
    $nim = $_POST['nim'];
    $nama_mahasiswa = $_POST['nama_mahasiswa'];
    $email = $_POST['email'];
    $prodi_id = $_POST['prodi_id'];
    $jurusan_id = $_POST['jurusan_id'];
    $tahun_masuk = $_POST['tahun_masuk'];
    $status = $_POST['status'];
    $jk = $_POST['jk'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    // Handle file upload if there is a file selected
    if (!empty($_FILES['fileToUpload']['name'])) {
        $target_dir = "../assets/mahasiswa/profile/";
        $target_file = $target_dir . basename($_FILES['fileToUpload']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($_FILES['fileToUpload']['tmp_name']);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES['fileToUpload']['size'] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($imageFileType, $allowed_types)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
                echo "The file " . htmlspecialchars(basename($_FILES['fileToUpload']['name'])) . " has been uploaded.";
                $profile_image = basename($_FILES['fileToUpload']['name']);
            } else {
                echo "Sorry, there was an error uploading your file.";
                $profile_image = null;
            }
        }
    } else {
        $profile_image = null;
    }

    // Update mahasiswa profile data in database
    try {
        $sql = "UPDATE mahasiswas 
                SET nim = ?, nama_mahasiswa = ?, email = ?, profile_image = IFNULL(?, profile_image), prodi_id = ?, jurusan_id = ?, tahun_masuk = ?, status = ?, jk = ?, alamat = ?, no_telp = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nim, $nama_mahasiswa, $email, $profile_image, $prodi_id, $jurusan_id, $tahun_masuk, $status, $jk, $alamat, $no_telp, $mahasiswa_id]);

        // Redirect to profile page after update
        header('Location: profile_mahasiswa.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
