<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'cdc')) {
    header('Location: ../../auth/login.php');
    exit;
}

// Include database connection file
include '../includes/db.php';

// Fetch detailed student data and their accepted job positions
function fetchStudentsAndJobs($pdo) {
    try {
        $sql = "SELECT m.id, m.nim, m.nama_mahasiswa, j.nama_jurusan AS jurusan, p.nama_prodi AS prodi, m.status, m.tahun_masuk, 
                       lm.status AS lamaran_status, l.nama_pekerjaan, pr.nama_perusahaan, lm.salary
                FROM mahasiswas m
                JOIN prodis p ON m.prodi_id = p.id
                JOIN jurusans j ON m.jurusan_id = j.id
                LEFT JOIN lamaran_mahasiswas lm ON m.id = lm.mahasiswa_id
                LEFT JOIN lowongan_kerja l ON lm.lowongan_id = l.id
                LEFT JOIN perusahaans pr ON l.perusahaan_id = pr.id";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching student and job data: " . $e->getMessage();
        return false;
    }
}

$students_and_jobs = fetchStudentsAndJobs($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa dan Pekerjaan</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../app/dist/css/adminlte.light.min.css" media="screen">
    <link rel="stylesheet" href="../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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

    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar inclusion -->
        <?php include 'navbar_cdc.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">Data Mahasiswa dan Pekerjaan</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active">Data Mahasiswa dan Pekerjaan</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <section class="col-lg-12 connectedSortable">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-trophy"></i>
                                        Data Mahasiswa dan Pekerjaan
                                    </h3>
                                    <div class="card-tools">
                                        <div class="input-group input-group-sm">
                                            <select id="statusFilter" class="form-control">
                                                <option value="">Semua Status</option>
                                                <option value="diterima">Diterima</option>
                                                <option value="ditolak">Ditolak</option>
                                            </select>
                                            <select id="prodiFilter" class="form-control">
                                                <option value="">Semua Prodi</option>
                                                <?php
                                                $sql = "SELECT id, nama_prodi FROM prodis";
                                                $stmt = $pdo->query($sql);
                                                while ($prodi = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$prodi['nama_prodi']}'>{$prodi['nama_prodi']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="mahasiswaTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th>Jurusan</th>
                                                    <th>Prodi</th>
                                                    <th>Status</th>
                                                    <th>Tahun Masuk</th>
                                                    <th>Nama Lowongan</th>
                                                    <th>Nama Perusahaan</th>
                                                    <th>Status Lamaran</th>
                                                    <th>Gaji</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($students_and_jobs as $index => $student): ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                    <td><?= htmlspecialchars($student['nim']) ?></td>
                                                    <td><?= htmlspecialchars($student['nama_mahasiswa']) ?></td>
                                                    <td><?= htmlspecialchars($student['jurusan']) ?></td>
                                                    <td><?= htmlspecialchars($student['prodi']) ?></td>
                                                    <td><?= htmlspecialchars($student['status']) ?></td>
                                                    <td><?= htmlspecialchars($student['tahun_masuk']) ?></td>
                                                    <td><?= htmlspecialchars($student['nama_pekerjaan'] ?? 'N/A') ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($student['nama_perusahaan'] ?? 'N/A') ?>
                                                    </td>
                                                    <td>
                                                        <?php if (strtolower($student['lamaran_status']) === 'diterima'): ?>
                                                            <span class="badge badge-success">Diterima</span>
                                                        <?php elseif (strtolower($student['lamaran_status']) === 'ditolak'): ?>
                                                            <span class="badge badge-danger">Ditolak</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Belum ada status</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($student['salary'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                            onclick="cetakPDF(<?= $student['id'] ?>)">
                                                            <i class="fas fa-print"></i> Cetak
                                                        </button>
                                                        </td>
                                                        </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../app/plugins/jquery/jquery.min.js"></script>
    <script src="../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../app/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../app/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../app/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../app/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../app/dist/js/adminlte.min.js"></script>
    <script>
    $(document).ready(function() {
        var table = $('#mahasiswaTable').DataTable();

        $('#statusFilter').on('change', function() {
            var status = $(this).val();
            table.column(9).search(status).draw();
        });

        $('#prodiFilter').on('change', function() {
            var prodi = $(this).val();
            table.column(4).search(prodi).draw();
        });
    });

    function cetakPDF(mahasiswaId) {
        window.location.href = 'cetak_pdf.php?id=' + mahasiswaId;
    }
    </script>
</body>

</html>