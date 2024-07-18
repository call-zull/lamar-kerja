<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Fetch student data based on user ID
function fetchStudentData($pdo, $user_id) {
    try {
        $sql = "SELECT nama_mahasiswa, nim, status FROM mahasiswas WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching student data: " . $e->getMessage();
        return false;
    }
}

// Fetch student statistics
function fetchStudentStatistics($pdo, $mahasiswa_id) {
    try {
        $statistics = [];

        // Count sent applications
        $sql = "SELECT COUNT(*) AS sent_applications FROM lamaran_mahasiswas WHERE mahasiswa_id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $statistics['sent_applications'] = $stmt->fetch(PDO::FETCH_ASSOC)['sent_applications'];

        // Count accepted applications
        $sql = "SELECT COUNT(*) AS accepted_applications FROM lamaran_mahasiswas WHERE mahasiswa_id = :mahasiswa_id AND status = 'Diterima'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $statistics['accepted_applications'] = $stmt->fetch(PDO::FETCH_ASSOC)['accepted_applications'];

        // Count certificates
        $sql = "SELECT COUNT(*) AS certificates FROM sertifikasi WHERE mahasiswa_id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $statistics['certificates'] = $stmt->fetch(PDO::FETCH_ASSOC)['certificates'];

        // Count trainings
        $sql = "SELECT COUNT(*) AS trainings FROM pelatihan WHERE mahasiswa_id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $statistics['trainings'] = $stmt->fetch(PDO::FETCH_ASSOC)['trainings'];

        // Count competitions
        $sql = "SELECT COUNT(*) AS competitions FROM lomba WHERE mahasiswa_id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $statistics['competitions'] = $stmt->fetch(PDO::FETCH_ASSOC)['competitions'];

        // Count projects
        $sql = "SELECT COUNT(*) AS projects FROM proyek WHERE mahasiswa_id = :mahasiswa_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
        $statistics['projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['projects'];

        return $statistics;
    } catch (PDOException $e) {
        echo "Error fetching student statistics: " . $e->getMessage();
        return false;
    }
}

$user_id = $_SESSION['user_id'];
$student_data = fetchStudentData($pdo, $user_id);
$statistics = fetchStudentStatistics($pdo, $student_data['id']);

// Date and Time Information
date_default_timezone_set('Asia/Makassar');
$days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];

$day = $days[date('l')];
$date = date('d');
$month = $months[date('F')];
$year = date('Y');
$time = date('H:i:s');
$currentDate = "$day, $date $month $year, $time WITA";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa Dashboard</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

    <!-- Include Navbar and Sidebar for mahasiswa -->
    <?php include 'navbar_mhs.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3 class="m-0">Dashboard</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Welcome Card -->
                <div class="row">
                    <div class="col-lg-8 col-12">
                        <div class="verified-card bg-primary">
                            <i class="fas fa-user"></i>
                            <h6>Selamat Datang, <?php echo htmlspecialchars($student_data['nama_mahasiswa']); ?> - <?php echo htmlspecialchars($student_data['nim']); ?></h6>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="date-card bg-info">
                            <i class="fas fa-calendar-alt"></i>
                            <h6>Hari ini: <?php echo $currentDate; ?></h6>
                        </div>
                    </div>
                </div>

                <!-- Status and Statistics -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($student_data['status']); ?></h3>
                                <p>Status Mahasiswa</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-university"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['sent_applications']); ?></h3>
                                <p>Lamaran Terkirim</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-paper-airplane"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['accepted_applications']); ?></h3>
                                <p>Lamaran Diterima</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['certificates']); ?></h3>
                                <p>Jumlah Sertifikat</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-document"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['trainings']); ?></h3>
                                <p>Pelatihan</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ribbon-a"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['competitions']); ?></h3>
                                <p>Lomba</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-trophy"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['projects']); ?></h3>
                                <p>Proyek</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-briefcase"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>

</div>

<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>

</body>
</html>
