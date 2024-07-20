<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
include '../includes/db.php';

function fetchAcceptedOrRejectedApplications($pdo, $mahasiswa_id) {
    try {
        $sql = "SELECT l.nama_pekerjaan, lm.pesan, lm.status, lm.salary
                FROM lamaran_mahasiswas lm
                JOIN lowongan_kerja l ON lm.lowongan_id = l.id
                WHERE lm.mahasiswa_id = :mahasiswa_id AND (lm.status = 'Diterima' OR lm.status = 'Ditolak')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}

$application_statuses = fetchAcceptedOrRejectedApplications($pdo, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Lamaran</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .content-wrapper {
            padding: 20px !important;
        }
        .content-header {
            padding: 20px 0 !important;
        }
        .card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <?php include 'navbar_mhs.php'; ?>
    
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Status Lamaran</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Status Lamaran</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($application_statuses): ?>
                            <?php foreach ($application_statuses as $status): ?>
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($status['nama_pekerjaan']); ?></h5>
                                        <p class="card-text"><strong>Pesan:</strong> <?php echo htmlspecialchars($status['pesan']); ?></p>
                                        <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($status['status']); ?></p>
                                        <p class="card-text"><strong>Salary:</strong> <?php echo htmlspecialchars($status['salary'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Belum ada lamaran yang diajukan dengan status diterima atau ditolak.</p>
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
</body>
</html>
