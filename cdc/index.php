<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'cdc') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Fetch overview statistics
function fetchOverviewStatistics($pdo) {
    try {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM mahasiswas) AS total_students,
                    (SELECT COUNT(*) FROM lamaran_mahasiswas) AS total_applications,
                    (SELECT COUNT(*) FROM lamaran_mahasiswas WHERE status = 'Diterima') AS accepted_applications,
                    (SELECT COUNT(*) FROM lamaran_mahasiswas WHERE status = 'Ditolak') AS rejected_applications";
        $stmt = $pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching overview statistics: " . $e->getMessage();
        return false;
    }
}

// Fetch data for charts
function fetchChartData($pdo) {
    try {
        $sql = "SELECT m.nama_mahasiswa, COUNT(lm.id) AS application_count
                FROM mahasiswas m
                LEFT JOIN lamaran_mahasiswas lm ON m.id = lm.mahasiswa_id
                GROUP BY m.id, m.nama_mahasiswa
                ORDER BY application_count DESC
                LIMIT 10";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching chart data: " . $e->getMessage();
        return false;
    }
}

$overview = fetchOverviewStatistics($pdo);
$chart_data = fetchChartData($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDC Dashboard</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'navbar_cdc.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
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
                    <!-- Overview cards -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <!-- Total Students -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $overview['total_students']; ?></h3>
                                    <p>Total Students</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Total Applications -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo $overview['total_applications']; ?></h3>
                                    <p>Total Applications</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-paper-airplane"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Accepted Applications -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo $overview['accepted_applications']; ?></h3>
                                    <p>Accepted Applications</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-checkmark"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Rejected Applications -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?php echo $overview['rejected_applications']; ?></h3>
                                    <p>Rejected Applications</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-close"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="row">
                        <section class="col-lg-7 connectedSortable">
                            <!-- Applications Chart -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-pie mr-1"></i>
                                        Applications by Student
                                    </h3>
                                    <div class="card-tools">
                                        <ul class="nav nav-pills ml-auto">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#chart-area" data-toggle="tab">Chart</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content p-0">
                                        <!-- Morris chart - Sales -->
                                        <div class="chart tab-pane active" id="chart-area" style="position: relative; height: 300px;">
                                            <canvas id="applicationsChart" height="300" style="height: 300px;"></canvas>
                                        </div>
                                    </div>
                                </div><!-- /.card-body -->
                            </div>
                        </section>
                        <section class="col-lg-5 connectedSortable">
                            <!-- Another chart or any other component can go here -->
                        </section>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div><!-- /.content-wrapper -->
    </div>

<script src="script_cdc.js"></script>
<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    // Chart.js - Applications Chart
    var ctx = document.getElementById('applicationsChart').getContext('2d');
    var applicationsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($chart_data, 'nama_mahasiswa')); ?>,
            datasets: [{
                label: 'Applications',
                data: <?php echo json_encode(array_column($chart_data, 'application_count')); ?>,
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
