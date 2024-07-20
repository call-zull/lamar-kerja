<?php
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include '../includes/db.php';

// Fetch student KHS and IPK information
$user_id = $_SESSION['user_id'];
$sql = "SELECT m.id, m.nama_mahasiswa, m.khs_semester_1, m.khs_semester_2, m.khs_semester_3, m.khs_semester_4,
               m.khs_semester_5, m.khs_semester_6, m.khs_semester_7, m.khs_semester_8,
               m.ipk_semester_1, m.ipk_semester_2, m.ipk_semester_3, m.ipk_semester_4,
               m.ipk_semester_5, m.ipk_semester_6, m.ipk_semester_7, m.ipk_semester_8
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

// Determine if the KHS files are PDFs or images
$khs_files = [];
$valid_semesters = 0;
for ($i = 1; $i <= 8; $i++) {
    $ipk_semester = 'ipk_semester_' . $i;
    if (!empty($profile[$ipk_semester])) {
        $valid_semesters = $i;
    }
    $khs_files[$i] = [
        'file' => $profile['khs_semester_' . $i],
        'isPdf' => false,
        'isImage' => false
    ];
    if (!empty($profile['khs_semester_' . $i])) {
        $fileType = strtolower(pathinfo($profile['khs_semester_' . $i], PATHINFO_EXTENSION));
        $khs_files[$i]['isPdf'] = $fileType === 'pdf';
        $khs_files[$i]['isImage'] = in_array($fileType, ['jpg', 'jpeg', 'png']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Hasil Studi</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <style>
        .preview-pdf, .preview-image {
            width: 100%;
            max-height: 500px;
        }
        .preview-container {
            display: none; /* Hide preview container by default */
        }
        .preview-container.show {
            display: block; /* Show preview container when needed */
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
                            <h1 class="m-0">Kartu Hasil Studi</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Kartu Hasil Studi</li>
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
                                    <h3 class="card-title">Upload Kartu Hasil Studi</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($message)): ?>
                                        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                                    <?php endif; ?>
                                    <form action="upload_khs.php" method="post" enctype="multipart/form-data" id="uploadForm">
                                        <div class="form-group">
                                        <label for="semester">Pilih Semester</label>
                                            <select class="form-control" id="semester" name="semester">
                                                <?php for ($i = 1; $i <= $valid_semesters; $i++): ?>
                                                    <option value="<?= $i ?>">Semester <?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="khs_file">KHS File (Maksimal 5 MB)</label>
                                            <input type="file" class="form-control-file" id="khs_file" name="khs_file" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <?php for ($i = 1; $i <= $valid_semesters; $i++): ?>
                                <?php if (!empty($khs_files[$i]['file'])): ?>
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h3 class="card-title">KHS Semester <?= $i ?></h3>
                                        </div>
                                        <div class="card-body preview-container" id="preview-container-<?= $i ?>">
                                            <?php if ($khs_files[$i]['isPdf']): ?>
                                                <iframe id="preview-pdf-<?= $i ?>" src="../assets/mahasiswa/khs/<?= htmlspecialchars($khs_files[$i]['file']) ?>" class="preview-pdf"></iframe>
                                            <?php elseif ($khs_files[$i]['isImage']): ?>
                                                <img id="preview-image-<?= $i ?>" src="../assets/mahasiswa/khs/<?= htmlspecialchars($khs_files[$i]['file']) ?>" alt="KHS Semester <?= $i ?>" class="preview-image">
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer">
                                            <button class="btn btn-info" onclick="showPreview(<?= $i ?>)">Lihat KHS</button>
                                            <form action="delete_khs.php" method="post" style="display: inline;">
                                                <input type="hidden" name="khs_semester" value="<?= $i ?>">
                                                <input type="hidden" name="khs_file" value="<?= htmlspecialchars($khs_files[$i]['file']) ?>">
                                                <button type="submit" class="btn btn-danger">Hapus KHS</button>
                                            </form>
                                            <button class="btn btn-warning" onclick="updateKHS(<?= $i ?>)">Perbarui KHS</button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endfor; ?>
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
        function showPreview(semester) {
            var previewContainer = document.getElementById('preview-container-' + semester);
            previewContainer.classList.add('show');
        }

        function updateKHS(semester) {
            document.getElementById('semester').value = semester;
            document.getElementById('khs_file').click();
        }
    </script>
</body>
</html>
