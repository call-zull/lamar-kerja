<?php
session_start();

// Check if user is authenticated as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_POST['id']; // Primary key ID admin
    $nama_admin = $_POST['nama_admin'];
    $prodi = $_POST['prodi'];
    $jurusan = $_POST['jurusan'];
    $email = $_POST['email'];

    // Update profile information
    $sql = "UPDATE admins SET nama_admin = ?, prodi_id = (SELECT id FROM prodis WHERE nama_prodi = ?), jurusan_id = (SELECT id FROM jurusans WHERE nama_jurusan = ?), email = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama_admin, $prodi, $jurusan, $email, $admin_id]);

    // Handle file upload if there is a file selected
    if (!empty($_FILES['fileToUpload']['name'])) {
        $target_dir = "../assets/admin/";
        $target_file = $target_dir . basename($_FILES['fileToUpload']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['fileToUpload']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
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
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
                // Update profile image in database
                $sql = "UPDATE admins SET profile_image = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([basename($_FILES['fileToUpload']['name']), $admin_id]);
                echo "The file ". htmlspecialchars(basename($_FILES['fileToUpload']['name'])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    header('Location: profile_admin.php');
}
?>
