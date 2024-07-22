<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'perusahaan') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Fetch company data based on user ID
function fetchCompanyData($pdo, $user_id) {
    try {
        $sql = "SELECT * FROM perusahaans WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching company data: " . $e->getMessage();
        return false;
    }
}

function fetchStatistics($pdo, $company_id) {
    try {
        // Fetch number of job vacancies
        $sql = "SELECT COUNT(*) AS job_count FROM lowongan_kerja WHERE perusahaan_id = :company_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['company_id' => $company_id]);
        $job_count = $stmt->fetch(PDO::FETCH_ASSOC)['job_count'];

        // Fetch number of applicants
        $sql = "SELECT COUNT(*) AS applicant_count FROM lamaran_mahasiswas lm JOIN lowongan_kerja lk ON lm.lowongan_id = lk.id WHERE lk.perusahaan_id = :company_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['company_id' => $company_id]);
        $applicant_count = $stmt->fetch(PDO::FETCH_ASSOC)['applicant_count'];

        // Fetch number of accepted applicants
        $sql = "SELECT COUNT(*) AS accepted_count FROM lamaran_mahasiswas lm JOIN lowongan_kerja lk ON lm.lowongan_id = lk.id WHERE lk.perusahaan_id = :company_id AND lm.status = 'Diterima'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['company_id' => $company_id]);
        $accepted_count = $stmt->fetch(PDO::FETCH_ASSOC)['accepted_count'];

        // Fetch number of rejected applicants
        $sql = "SELECT COUNT(*) AS rejected_count FROM lamaran_mahasiswas lm JOIN lowongan_kerja lk ON lm.lowongan_id = lk.id WHERE lk.perusahaan_id = :company_id AND lm.status = 'Ditolak'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['company_id' => $company_id]);
        $rejected_count = $stmt->fetch(PDO::FETCH_ASSOC)['rejected_count'];

        // Calculate percentage
        $total_applicants = $applicant_count;
        $accepted_percentage = ($total_applicants > 0) ? round(($accepted_count / $total_applicants) * 100) : 0;
        $rejected_percentage = ($total_applicants > 0) ? round(($rejected_count / $total_applicants) * 100) : 0;

        return [
            'job_count' => $job_count,
            'applicant_count' => $applicant_count,
            'accepted_count' => $accepted_count,
            'rejected_count' => $rejected_count,
            'accepted_percentage' => $accepted_percentage,
            'rejected_percentage' => $rejected_percentage,
        ];
    } catch (PDOException $e) {
        echo "Error fetching statistics: " . $e->getMessage();
        return false;
    }
}

$company_data = fetchCompanyData($pdo, $_SESSION['user_id']);
$statistics = fetchStatistics($pdo, $company_data['id']);

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
    <title>Perusahaan Dashboard</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
        .verified-card, .date-card {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .verified-card {
            background-color: #28a745;
            color: #fff;
        }
        .date-card {
            background-color: #17a2b8;
            color: #fff;
        }
        .verified-card i, .date-card i {
            font-size: 25px;
            margin-right: 10px;
        }
        .verified-card h5, .date-card h5 {
            margin: 0;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Include Navbar and Sidebar for company -->
    <?php include 'navbar_perusahaan.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">Dashboard</h4>
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
                        <div class="verified-card">
                            <i class="fas fa-check-circle"></i>
                            <h6>Selamat Datang, <?php echo htmlspecialchars($company_data['nama_perusahaan']); ?></h6>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="date-card">
                            <i class="fas fa-calendar-alt"></i>
                            <h6>Hari ini: <?php echo $currentDate; ?></h6>
                        </div>
                    </div>
                </div>
                
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['job_count']); ?></h3>
                                <p>Jumlah Lowongan</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-briefcase"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['applicant_count']); ?></h3>
                                <p>Jumlah Pelamar</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['accepted_count']); ?></h3>
                                <p>Pelamar Diterima (<?php echo htmlspecialchars($statistics['accepted_percentage']); ?>%)</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo htmlspecialchars($statistics['rejected_count']); ?></h3>
                                <p>Pelamar Ditolak (<?php echo htmlspecialchars($statistics['rejected_percentage']); ?>%)</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-close"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main row -->
                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-7 connectedSortable">
                        <!-- Custom tabs (Charts with tabs)-->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i>
                                    Statistik Lowongan dan Pelamar
                                </h3>
                                <div class="card-tools">
                                    <ul class="nav nav-pills ml-auto">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#lowongan-chart" data-toggle="tab">Lowongan</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#pelamar-chart" data-toggle="tab">Pelamar</a>
                                        </li>
                                    </ul>
                                </div>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <!-- Morris chart - Sales -->
                                    <div class="chart tab-pane active" id="lowongan-chart" style="position: relative; height: 300px;">
                                        <canvas id="lowongan-chart-canvas" height="300" style="height: 300px;"></canvas>
                                    </div>
                                    <div class="chart tab-pane" id="pelamar-chart" style="position: relative; height: 300px;">
                                        <canvas id="pelamar-chart-canvas" height="300" style="height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </section>
                    <!-- /.Left col -->
                    <section class="col-lg-5 connectedSortable">
                        <!-- Another chart or any other component can go here -->
                    </section>
                </div>
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->

<!-- Include external JS file -->
<script src="script_perusahaan.js"></script>
<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx1 = document.getElementById('lowongan-chart-canvas').getContext('2d');
    var lowonganChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: ['Jumlah Lowongan', 'Jumlah Pelamar'],
            datasets: [{
                data: [<?php echo $statistics['job_count']; ?>, <?php echo $statistics['applicant_count']; ?>],
                backgroundColor: ['#6c757d','#007bff']
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true
        }
    });

    // Chart.js - Pelamar Chart
    var ctx2 = document.getElementById('pelamar-chart-canvas').getContext('2d');
    var pelamarChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Pelamar Diterima', 'Pelamar Ditolak'],
            datasets: [{
                data: [<?php echo $statistics['accepted_count']; ?>, <?php echo $statistics['rejected_count']; ?>],
                backgroundColor: ['#28a745', '#dc3545']
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true
        }
    });
</script>
</body>
</html>
