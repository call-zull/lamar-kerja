<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
include '../includes/db.php';

function fetchMahasiswaId($pdo, $user_id) {
    try {
        $sql = "SELECT id FROM mahasiswas WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}

function fetchApplications($pdo, $mahasiswa_id, $filter_status) {
    try {
        $sql = "SELECT l.nama_pekerjaan, lm.pesan, lm.status, lm.salary
                FROM lamaran_mahasiswas lm
                JOIN lowongan_kerja l ON lm.lowongan_id = l.id
                WHERE lm.mahasiswa_id = :mahasiswa_id";

        if ($filter_status && $filter_status !== 'all') {
            $sql .= " AND lm.status = :status";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['mahasiswa_id' => $mahasiswa_id, 'status' => $filter_status]);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}

$mahasiswa_id = fetchMahasiswaId($pdo, $user_id);
if ($mahasiswa_id === false) {
    $_SESSION['error_message'] = "Error retrieving mahasiswa ID.";
    header('Location: cari_kerja.php');
    exit;
}

$filter_status = isset($_POST['filter_status']) ? $_POST['filter_status'] : 'all';
$application_statuses = fetchApplications($pdo, $mahasiswa_id, $filter_status);
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
        .status-lamaran-card {
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
            position: relative;
        }
        .status-lamaran-card.pending {
            border-left-color: #ffc107;
        }
        .status-lamaran-card.diterima {
            border-left-color: #28a745;
        }
        .status-lamaran-card.ditolak {
            border-left-color: #dc3545;
        }
        .status-lamaran-card .card-title {
            font-weight: bold;
            font-size: 1.2em;
        }
        .status-lamaran-card .card-text {
            margin-bottom: 10px;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
            color: #fff;
        }
        .status-badge.pending {
            background-color: #ffc107;
        }
        .status-badge.diterima {
            background-color: #28a745;
        }
        .status-badge.ditolak {
            background-color: #dc3545;
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
                <form method="POST" action="status_lamaran.php">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select name="filter_status" class="form-control" onchange="this.form.submit()">
                                <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>Semua Status</option>
                                <option value="Pending" <?php echo $filter_status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Diterima" <?php echo $filter_status == 'Diterima' ? 'selected' : ''; ?>>Diterima</option>
                                <option value="Ditolak" <?php echo $filter_status == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($application_statuses): ?>
                            <?php foreach ($application_statuses as $status): ?>
                                <?php
                                    $statusClass = strtolower($status['status']);
                                    $salary = $status['salary'] ?? 'N/A';
                                ?>
                                <div class="card status-lamaran-card <?php echo $statusClass; ?>">
                                    <div class="card-body">
                                        <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status['status']); ?></span>
                                        <h5 class="card-title"><?php echo htmlspecialchars($status['nama_pekerjaan']); ?></h5>
                                        <p class="card-text"><strong>Pesan:</strong> <?php echo htmlspecialchars($status['pesan']); ?></p>
                                        <p class="card-text"><strong>Salary:</strong> <?php echo htmlspecialchars($salary); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Belum ada lamaran yang diajukan.</p>
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
