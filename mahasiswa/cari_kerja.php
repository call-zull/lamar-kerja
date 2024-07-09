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
    <?php include 'navbar_mhs.php'; ?>

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
                            <li class="breadcrumb-item active">Cari Kerja</li>
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
                                <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalLamar' 
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
<div class="modal fade" id="modalLamar" tabindex="-1" aria-labelledby="modalLamarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="crud_cari_kerja/proses_lamar_kerja.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLamarLabel">Lamar Lowongan Pekerjaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="lowongan_id" name="lowongan_id">
                    <input type="hidden" id="mahasiswa_id" name="mahasiswa_id">
                    <div class="form-group">
                        <label for="pesan">Tulis Pesan</label>
                        <textarea class="form-control" id="pesan" name="pesan" rows="3" required></textarea>
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

    $('#modalLamar').on('show.bs.modal', function (event) {
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
