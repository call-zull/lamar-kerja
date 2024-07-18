<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin')) {
    header('Location: ../../auth/login.php');
    exit;
}

// Include database connection file
include '../../includes/db.php';

try {
    // Fetch student data using PDO
    $sql = "SELECT m.id, m.nim, m.nama_mahasiswa, j.nama_jurusan AS jurusan, p.nama_prodi AS prodi, m.status, m.tahun_masuk
            FROM mahasiswas m
            JOIN prodis p ON m.prodi_id = p.id
            JOIN jurusans j ON m.jurusan_id = j.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch prodi data for the filter dropdown
    $sql = "SELECT id, nama_prodi FROM prodis";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $prodis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Portofolio Mahasiswa</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.dark.min.css" media="screen">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.light.min.css" media="screen">
    <link rel="stylesheet" href="../../app/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../app/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <style>
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100% - 1rem);
        }

        .modal-lg-custom {
            width: 100%;
            max-width: 90%;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .nav-sidebar .nav-link.active {
            background-color: #343a40 !important;
        }

        .context-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            border: 1px solid #ddd;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .context-menu a {
            color: #333;
            display: block;
            padding: 8px 10px;
            text-decoration: none;
        }

        .context-menu a:hover {
            background-color: #f2f2f2;
        }
        .table-responsive {
            overflow-y: auto;
            max-height: 400px; /* Adjust height as needed */
        }

        .table-responsive thead {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #343a40; /* Ensure the header has a background */
        }

        .table-responsive thead th {
        color: #ffffff;
        border-color: #454d55; 
        }

        .table-responsive tbody tr:hover {
        background-color: #f2f2f2; 
        } 
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar inclusion -->
        <?php include 'navbar_admin.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">Tampil Data Portofolio Mahasiswa</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active">Data Portofolio</li>
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
                                        Data Portofolio Mahasiswa
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <!-- Dropdown filter for prodi -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <select id="prodiFilter" class="form-control">
                                                <option value="">Filter by Prodi</option>
                                                <?php foreach ($prodis as $prodi): ?>
                                                    <option value="<?= htmlspecialchars($prodi['nama_prodi']) ?>"><?= htmlspecialchars($prodi['nama_prodi']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
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
                                                    <th>Sertifikasi</th>
                                                    <th>Lomba</th>
                                                    <th>Pelatihan</th>
                                                    <th>Proyek</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($students as $index => $student): ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                        <td><?= htmlspecialchars($student['nim']) ?></td>
                                                        <td><?= htmlspecialchars($student['nama_mahasiswa']) ?></td>
                                                        <td><?= htmlspecialchars($student['jurusan']) ?></td>
                                                        <td><?= htmlspecialchars($student['prodi']) ?></td>
                                                        <td><?= htmlspecialchars($student['status']) ?></td>
                                                        <td><?= htmlspecialchars($student['tahun_masuk']) ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sertifikasiModal" data-id="<?= $student['id'] ?>">
                                                                <i class="fas fa-eye"></i> Lihat
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#lombaModal" data-id="<?= $student['id'] ?>">
                                                                <i class="fas fa-eye"></i> Lihat 
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#pelatihanModal" data-id="<?= $student['id'] ?>">
                                                                <i class="fas fa-eye"></i> Lihat 
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#proyekModal" data-id="<?= $student['id'] ?>">
                                                                <i class="fas fa-eye"></i> Lihat 
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-secondary btn-sm" onclick="cetakPDF(<?= $student['id'] ?>)">
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

            <!-- Sertifikasi Modal -->
            <div class="modal fade" id="sertifikasiModal" tabindex="-1" role="dialog" aria-labelledby="sertifikasiModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="sertifikasiModalLabel">Sertifikasi Mahasiswa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="sertifikasiContent"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lomba Modal -->
            <div class="modal fade" id="lombaModal" tabindex="-1" role="dialog" aria-labelledby="lombaModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="lombaModalLabel">Lomba Mahasiswa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="lombaContent"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pelatihan Modal -->
            <div class="modal fade" id="pelatihanModal" tabindex="-1" role="dialog" aria-labelledby="pelatihanModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="pelatihanModalLabel">Pelatihan Mahasiswa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="pelatihanContent"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proyek Modal -->
            <div class="modal fade" id="proyekModal" tabindex="-1" role="dialog" aria-labelledby="proyekModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="proyekModalLabel">Proyek Mahasiswa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="proyekContent"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../script_admin.js"></script>
    <script src="../../app/plugins/jquery/jquery.min.js"></script>
    <script src="../../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../app/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../app/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../app/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../app/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../app/dist/js/adminlte.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#mahasiswaTable').DataTable();

            $('#prodiFilter').on('change', function() {
                var selectedProdi = $(this).val();
                table.columns(4).search(selectedProdi).draw();
            });

            $('#sertifikasiModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var mahasiswaId = button.data('id');

                $.ajax({
                    url: 'get_sertifikasi.php',
                    type: 'POST',
                    data: { id: mahasiswaId },
                    success: function(response) {
                        $('#sertifikasiContent').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            });

            $('#lombaModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var mahasiswaId = button.data('id');

                $.ajax({
                    url: 'get_lomba.php',
                    type: 'POST',
                    data: { id: mahasiswaId },
                    success: function(response) {
                        $('#lombaContent').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            });

            $('#pelatihanModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var mahasiswaId = button.data('id');

                $.ajax({
                    url: 'get_pelatihan.php',
                    type: 'POST',
                    data: { id: mahasiswaId },
                    success: function(response) {
                        $('#pelatihanContent').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            });

            $('#proyekModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var mahasiswaId = button.data('id');

                $.ajax({
                    url: 'get_proyek.php',
                    type: 'POST',
                    data: { id: mahasiswaId },
                    success: function(response) {
                        $('#proyekContent').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            });
        });

        function cetakPDF(mahasiswaId) {
            window.location.href = 'cetak_pdf.php?id=' + mahasiswaId;
        }
    </script>
</body>
</html>
