<?php
session_start();

// Check if user is authenticated as perusahaan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $perusahaan_id = $_POST['id'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $alamat_perusahaan = $_POST['alamat_perusahaan'];
    $email_perusahaan = $_POST['email_perusahaan'];
    $jenis_perusahaan = $_POST['jenis_perusahaan'];
    $tahun_didirikan = $_POST['tahun_didirikan'];
    $pimpinan_perusahaan = $_POST['pimpinan_perusahaan'];
    $deskripsi_perusahaan = $_POST['deskripsi_perusahaan'];
    $no_telp = $_POST['no_telp'];

    // Handle file upload if there is a file selected
    if (!empty($_FILES['fileToUpload']['name'])) {
        $target_dir = "../assets/perusahaan/profile/";
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
            }
        }
    } else {
        // If no new image was uploaded, keep the current image
        $profile_image = null;
    }

    try {
        // Update perusahaan profile data in database
        $sql = "UPDATE perusahaans 
                SET nama_perusahaan = ?, alamat_perusahaan = ?, email_perusahaan = ?, jenis_perusahaan_id = ?, tahun_didirikan = ?, pimpinan_perusahaan = ?, deskripsi_perusahaan = ?, profile_image = COALESCE(?, profile_image), no_telp = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama_perusahaan, $alamat_perusahaan, $email_perusahaan, $jenis_perusahaan, $tahun_didirikan, $pimpinan_perusahaan, $deskripsi_perusahaan, $profile_image, $no_telp, $perusahaan_id]);

        // Redirect to profile page after update
        header('Location: profile_perusahaan.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
