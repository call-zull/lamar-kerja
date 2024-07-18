<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/db.php';


// Fetch admin data based on user ID
function fetchAdminData($pdo, $user_id) {
    try {
        $sql = "SELECT * FROM admins WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching admin data: " . $e->getMessage();
        return false;
    }
}

// Ambil data jumlah pengguna berdasarkan role dari tabel users
$sql = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counts = [
    'mahasiswa' => 0,
    'alumni' => 0,
    'cdc' => 0,
    'perusahaan' => 0,
    'admin' => 0,
];

foreach ($results as $row) {
    $counts[$row['role']] = $row['count'];
}

// Ambil data jumlah mahasiswa aktif dan alumni dari tabel mahasiswas
$sql = "SELECT status, COUNT(*) as count FROM mahasiswas GROUP BY status";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mahasiswa_counts = [
    'Mahasiswa Aktif' => 0,
    'Alumni' => 0,
];

foreach ($results as $row) {
    $mahasiswa_counts[$row['status']] = $row['count'];
}

// Fetch departments from the jurusans table
$sql = "SELECT id, nama_jurusan FROM jurusans";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$jurusans = $stmt->fetchAll(PDO::FETCH_ASSOC);


$admin_data = fetchAdminData($pdo, $_SESSION['user_id']);

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
    <title>Admin Dashboard</title>
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
                    <div class="col-lg-8 col-12">
                        <div class="verified-card">
                            <i class="fas fa-user"></i>
                            <h6>Selamat Datang, <?php echo htmlspecialchars($admin_data['nama_admin']); ?></h6>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="date-card">
                            <i class="fas fa-calendar-alt"></i>
                            <h6>Hari ini: <?php echo $currentDate; ?></h6>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $mahasiswa_counts['Mahasiswa Aktif']; ?></h3>
                                    <p>Jumlah Mahasiswa Aktif</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h3><?php echo $mahasiswa_counts['Alumni']; ?></h3>
                                    <p>Jumlah Alumni</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
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
                <i class="fas fa-users""></i>
            </div>
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
                <i class="fas fa-building"></i>
            </div>
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
                <i class="fas fa-user-tie"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
</div>


                    <!-- Chart Row -->
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Reports <span>/Today</span></h5>
                                    <!-- Dropdown for jurusan -->
                                    <select id="jurusanFilter" class="form-control mb-3">
                                        <option value="">Select Jurusan</option>
                                        <?php foreach ($jurusans as $jurusan) : ?>
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
                                                    data: [],
                                                }, {
                                                    name: 'Pelatihan',
                                                    data: []
                                                }, {
                                                    name: 'Sertifikasi',
                                                    data: []
                                                }, {
                                                    name: 'Proyek',
                                                    data: []
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
                                                    categories: []
                                                },
                                                tooltip: {
                                                    x: {
                                                        format: 'dd/MM/yyyy'
                                                    },
                                                }
                                            });

                                            chart.render();

                                            document.getElementById('jurusanFilter').addEventListener('change', function() {
                                                const jurusanId = this.value;
                                                fetch(`fetch_chart_data.php?jurusan_id=${jurusanId}`)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        const dates = data.lomba.map(item => item.date);

                                                        // If dates are empty, set a placeholder date to prevent issues
                                                        if (dates.length === 0) {
                                                            dates.push(new Date().toISOString().split('T')[0]);
                                                        }

                                                        chart.updateSeries([{
                                                            name: 'Lomba',
                                                            data: data.lomba.map(item => item.count),
                                                        }, {
                                                            name: 'Pelatihan',
                                                            data: data.pelatihan.map(item => item.count)
                                                        }, {
                                                            name: 'Sertifikasi',
                                                            data: data.sertifikasi.map(item => item.count)
                                                        }, {
                                                            name: 'Proyek',
                                                            data: data.proyek.map(item => item.count)
                                                        }]);
                                                        chart.updateOptions({
                                                            xaxis: {
                                                                categories: dates
                                                            }
                                                        });
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
    <script src="script_admin.js"></script>
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</body>

</html>