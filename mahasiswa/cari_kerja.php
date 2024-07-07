<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Dapatkan user_id dari sesi
$user_id = $_SESSION['user_id'];

function fetchJobs($pdo) {
    try {
        $sql = "SELECT * FROM lowongan_kerja";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

include '../includes/db.php';

// Fetching jobs
$jobs = fetchJobs($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Kerja</title>
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
            margin-left: 250px !important;
            padding: 20px !important;
        }
        .content-header {
            padding: 20px 0 !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">SI Portofolio</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#" id="dark-mode-toggle" role="button">
                    <i class="fas fa-moon"></i>
                    Dark Mode
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="light-mode-toggle" role="button">
                    <i class="fas fa-sun"></i>
                    Light Mode
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <img src="../app/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">SI Portofolio</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="index.php" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <!-- Profile -->
                    <li class="nav-item">
                        <a href="profile_mahasiswa.php" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Profile</p>
                        </a>
                    </li>
                    <!-- Data Portofolio -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Data Portofolio <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- Kompetensi -->
                            <li class="nav-item">
                                <a href="data_kompetensi.php" class="nav-link">
                                    <i class="fas fa-tasks nav-icon"></i>
                                    <p>Kompetensi</p>
                                </a>
                            </li>
                            <!-- Lomba -->
                            <li class="nav-item">
                                <a href="data_lomba.php" class="nav-link">
                                    <i class="fas fa-trophy nav-icon"></i>
                                    <p>Lomba</p>
                                </a>
                            </li>
                            <!-- Pelatihan -->
                            <li class="nav-item">
                                <a href="data_pelatihan.php" class="nav-link">
                                    <i class="fas fa-chalkboard-teacher nav-icon"></i>
                                    <p>Pelatihan</p>
                                </a>
                            </li>
                            <!-- Proyek -->
                            <li class="nav-item">
                                <a href="data_proyek.php" class="nav-link">
                                    <i class="far fa-folder-open nav-icon"></i>
                                    <p>Proyek</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Cari Kerja -->
                    <li class="nav-item">
                        <a href="cari_kerja.php" class="nav-link">
                            <i class="nav-icon fas fa-briefcase"></i>
                            <p>Cari Kerja</p>
                        </a>
                    </li>
                    <!-- Logout -->
                    <li class="nav-item">
                        <a href="../auth/logout.php" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Cari Lowongan yang Sesuai</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Perusahaan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Alert Section -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <div class="row mb-3">
                    <div class="col-md-10">
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="row">
                    <?php
                    // Check apakah ada data yang diambil
                    if ($jobs) {
                        foreach ($jobs as $row) {
                    ?>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['nama_pekerjaan']); ?></h5>
                                <p class="card-text"><strong>Posisi:</strong> <?php echo htmlspecialchars($row['posisi']); ?></p>
                                <p class="card-text"><strong>Kualifikasi:</strong> <?php echo htmlspecialchars($row['kualifikasi']); ?></p>
                                <p class="card-text"><strong>Tanggal Posting:</strong> <?php echo htmlspecialchars($row['tanggal_posting']); ?></p>
                                <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalEdit' 
                                    data-id="<?php echo $row['id']; ?>"
                                    data-mahasiswa="<?php echo $user_id; ?>">Lamar</button>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo "<p>Tidak ada data</p>";
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal Lamar -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="crud_cari_kerja/proses_lamar_kerja.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Lamar Lowongan Pekerjaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="lowongan_id" name="lowongan_id">
                    <input type="hidden" id="mahasiswa_id" name="mahasiswa_id">
                    <div class="form-group">
                        <label for="edit_nama_pekerjaan">Tulis Pesan</label>
                        <input type="text" class="form-control" id="edit_nama_pekerjaan" name="pesan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../app/plugins/jquery/jquery.min.js"></script>
<script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../app/dist/js/adminlte.min.js"></script>
<script src="../app/dist/js/demo.js"></script>
<script>
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    darkModeToggle.addEventListener('click', function () {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
    });

    const lightModeToggle = document.getElementById('light-mode-toggle');
    lightModeToggle.addEventListener('click', function () {
        document.body.classList.remove('dark-mode');
        document.body.classList.add('light-mode');
    });

    $('#modalEdit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var mahasiswa = button.data('mahasiswa');

        var modal = $(this);
        modal.find('#lowongan_id').val(id);
        modal.find('#mahasiswa_id').val(mahasiswa);
    });
</script>

</body>
</html>
