<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';

// Ambil data jumlah pengguna berdasarkan role
$sql = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counts = [
    'mhs' => 0,
    'cdc' => 0,
    'perusahaan' => 0,
    'admin' => 0,
];

foreach ($results as $row) {
    $counts[$row['role']] = $row['count'];
}

$days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];

$day = $days[date('l')];
$date = date('d');
$month = $months[date('F')];
$year = date('Y');
$currentDate = "$day, $date $month $year";

// Fetch departments from the jurusans table
$sql = "SELECT id, nama_jurusan FROM jurusans";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$jurusans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- ApexCharts CSS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

    <!-- Include Navbar and Sidebar for admin -->
    <?php include 'navbar_admin.php'; ?>

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
                    <div class="col-lg-6 col-12">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <h5>Selamat Datang, Admin</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="card bg-info">
                            <div class="card-body">
                                <h5>Hari ini: <?php echo $currentDate; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo $counts['mhs']; ?></h3>
                                <p>Jumlah Mahasiswa</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo $counts['cdc']; ?></h3>
                                <p>Jumlah CDC</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $counts['perusahaan']; ?></h3>
                                <p>Jumlah Perusahaan</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo $counts['admin']; ?></h3>
                                <p>Jumlah Admin</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

                <!-- Chart Row -->
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Reports <span>/Today</span></h5>
                                <!-- Dropdown for jurusan -->
                                <select id="jurusanFilter" class="form-control mb-3">
                                    <option value="">Select Jurusan</option>
                                    <?php foreach ($jurusans as $jurusan): ?>
                                        <option value="<?php echo $jurusan['id']; ?>"><?php echo $jurusan['nama_jurusan']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!-- Line Chart -->
                                <div id="reportsChart"></div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        const chart = new ApexCharts(document.querySelector("#reportsChart"), {
                                            series: [{
                                                name: 'Lomba',
                                                data: [31, 40, 28, 51, 42, 82, 56],
                                            }, {
                                                name: 'Pelatihan',
                                                data: [11, 32, 45, 32, 34, 52, 41]
                                            }, {
                                                name: 'Sertifikasi',
                                                data: [15, 11, 32, 18, 9, 24, 11]
                                            }, {
                                                name: 'Proyek',
                                                data: [20, 30, 15, 35, 25, 45, 30]
                                            }],
                                            chart: {
                                                height: 350,
                                                type: 'area',
                                                toolbar: {
                                                    show: false
                                                },
                                            },
                                            markers: {
                                                size: 4
                                            },
                                            colors: ['#4154f1', '#2eca6a', '#ff771d', '#00cc99'],
                                            fill: {
                                                type: "gradient",
                                                gradient: {
                                                    shadeIntensity: 1,
                                                    opacityFrom: 0.3,
                                                    opacityTo: 0.4,
                                                    stops: [0, 90, 100]
                                                }
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            stroke: {
                                                curve: 'smooth',
                                                width: 2
                                            },
                                            xaxis: {
                                                type: 'datetime',
                                                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
                                            },
                                            tooltip: {
                                                x: {
                                                    format: 'dd/MM/yy HH:mm'
                                                },
                                            }
                                        });

                                        chart.render();

                                        // Fetch and update chart data based on jurusan
                                        document.getElementById('jurusanFilter').addEventListener('change', function() {
                                            const jurusanId = this.value;
                                            fetch(`fetch_chart_data.php?jurusan_id=${jurusanId}`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    chart.updateSeries([{
                                                        name: 'Lomba',
                                                        data: data.lomba,
                                                    }, {
                                                        name: 'Pelatihan',
                                                        data: data.pelatihan
                                                    }, {
                                                        name: 'Sertifikasi',
                                                        data: data.sertifikasi
                                                    }, {
                                                        name: 'Proyek',
                                                        data: data.proyek
                                                    }]);
                                                });
                                        });
                                    });
                                </script>
                                <!-- End Line Chart -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.chart row -->

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->
</div>

<!-- Include external JS file -->
<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
</body>
</html>
