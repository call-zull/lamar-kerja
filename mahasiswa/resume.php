<?php
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

// Fetch student resume information
$user_id = $_SESSION['user_id'];
$sql = "SELECT m.id, m.nama_mahasiswa, m.resume 
        FROM mahasiswas m
        WHERE m.user_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

// Check if student data is found
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profile) {
    die("Data mahasiswa tidak ditemukan.");
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Determine if the resume is a PDF or an image
$isPdf = false;
$isImage = false;
if (!empty($profile['resume'])) {
    $fileType = strtolower(pathinfo($profile['resume'], PATHINFO_EXTENSION));
    $isPdf = $fileType === 'pdf';
    $isImage = in_array($fileType, ['jpg', 'jpeg', 'png']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <style>
        .preview-pdf, .preview-image {
            width: 100%;
            max-height: 500px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'navbar_mhs.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Resume</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Resume</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Upload Resume</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($message)): ?>
                                        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                                    <?php endif; ?>
                                    <form action="upload_resume.php" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="resume">Silahkan klik choose file untuk upload file, maksimal 5 MB</label>
                                            <input type="file" class="form-control-file" id="resume" name="resume" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </form>
                                    <?php if (!empty($profile['resume'])): ?>
                                        <hr>
                                        <h4>Resume Anda</h4>
                                        <button class="btn btn-info" onclick="showPreview()">Lihat Resume</button>
                                        <form action="delete_resume.php" method="post" style="display: inline;">
                                            <input type="hidden" name="resume" value="<?= htmlspecialchars($profile['resume']) ?>">
                                            <button type="submit" class="btn btn-danger">Hapus Resume</button>
                                        </form>
                                        <button class="btn btn-warning" onclick="document.getElementById('resume').click();">Perbarui Resume</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <?php if (!empty($profile['resume'])): ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Preview Resume</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($isPdf): ?>
                                            <iframe id="preview-pdf" src="../assets/mahasiswa/resume/<?= htmlspecialchars($profile['resume']) ?>" class="preview-pdf" style="display: none;"></iframe>
                                        <?php elseif ($isImage): ?>
                                            <img id="preview-image" src="../assets/mahasiswa/resume/<?= htmlspecialchars($profile['resume']) ?>" alt="Resume" class="preview-image" style="display: none;">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script>
        function showPreview() {
            <?php if ($isPdf): ?>
                document.getElementById('preview-pdf').style.display = 'block';
                document.getElementById('preview-image').style.display = 'none';
            <?php elseif ($isImage): ?>
                document.getElementById('preview-image').style.display = 'block';
                document.getElementById('preview-pdf').style.display = 'none';
            <?php endif; ?>
        }
    </script>
</body>
</html>
