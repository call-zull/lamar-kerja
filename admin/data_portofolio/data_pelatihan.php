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
    <title>Data Pelatihan</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../app/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../app/dist/css/adminlte.min.css">
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
        max-width: 90%; /* Set maximum width to 90% of the viewport */
    }

    .modal-body {
        max-height: 70vh; /* Adjust the max-height of modal body */
        overflow-y: auto; /* Add vertical scroll if content exceeds max-height */
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
        <?php include 'navbar_admin.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">Tampil Data Pelatihan Mahasiswa</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active">Data Pelatihan</li>
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
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        Data Pelatihan Mahasiswa
                                    </h3>
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
                                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#pelatihanModal" data-id="<?= $student['id'] ?>" data-nim="<?= htmlspecialchars($student['nim']) ?>">
                                                                <i class="fas fa-eye"></i> Lihat Pelatihan
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

            <!-- Pelatihan Modal -->
            <div class="modal fade" id="pelatihanModal" tabindex="-1" role="dialog" aria-labelledby="pelatihanModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg-custom" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="pelatihanModalLabel">Pelatihan Mahasiswa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <div id="pelatihanContent"></div>
                            </div>
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
    <script src="../../app/plugins/jquery/jquery.min.js"></script>
    <script src="../../app/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../app/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../app/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../app/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../app/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../app/dist/js/adminlte.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#mahasiswaTable').DataTable();

            $('#pelatihanModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var mahasiswaId = button.data('id');
                var mahasiswaNim = button.data('nim');

                // Set NIM Mahasiswa in modal header
                var modalTitle = 'Pelatihan Mahasiswa - ' + mahasiswaNim;
                $('#pelatihanModalLabel').text(modalTitle);

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
        });
    </script>
</body>
</html>
